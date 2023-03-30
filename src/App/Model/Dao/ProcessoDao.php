<?php

namespace App\Model\Dao;

use App\Controller\ProcessoController;
use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Processo;
use Core\Model\AppDao;
use Core\Util\Functions;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use ReflectionException;

class ProcessoDao extends AppDao
{

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Processo::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @throws ORMException
     */
    function listar()
    {
        $sql = "SELECT p FROM \App\Model\Processo p "
            . " JOIN p.tramites t WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto"
            . " JOIN t.setorAtual sa"
            . " WHERE p.isArquivado=false";
        $sql .= $this->getCommonFilter();
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @throws ORMException
     * @throws \Exception
     */
    function buscarQuantidade($tipo): ?array
    {
//        TODO: Criar view com os contadores.
        if ($tipo === "contribuintes") {
            $sql = "SELECT COUNT(p.id) as qtde FROM processo p " .
                "LEFT JOIN tramite t ON t.processo_id=p.id AND t.numero_fase=p.numero_fase AND t.assunto_id=p.assunto_id " .
                "AND t.is_cancelado=0 AND t.is_despachado=0 LEFT JOIN interessado i ON p.interessado_id = i.id " .
                "WHERE p.id IS NOT NULL AND p.is_arquivado=0 AND t.is_recebido=0 AND i.is_externo=1 AND p.numero is null";
        } else {
            $sql = "SELECT COUNT(p.id) as qtde FROM processo p " .
                "LEFT JOIN tramite t ON t.processo_id=p.id " .
                "AND t.numero_fase=p.numero_fase AND t.assunto_id=p.assunto_id  AND t.is_cancelado=0 AND t.is_despachado=0 WHERE 1";
        }
        $exercicio = ProcessoController::getExercicioAtual();
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        if ($exercicio != null && $exercicio != 'todos' ) {
            $sql .= " AND p.exercicio=$exercicio";
        }
        if ($usuario != null && $tipo != 'enviados' && $usuario->getTipo() != TipoUsuario::MASTER) {
            $setores = $usuario->getSetoresIds(true);
            if(!empty($setores)){
                $sql .= " AND t.setor_atual_id IN($setores)";
            }
            $sql .= " AND IF(usuario_destino_id IS NOT NULL,usuario_destino_id={$usuario->getId()},1)=1";
        }
        switch ($tipo) {
            case 'enviados':
                if ($usuario != null)
                    $sql .= " AND t.usuario_envio_id={$usuario->getId()} AND numero is not null";
                break;
            case 'receber':
                $sql .= " AND t.is_recebido=0 AND p.is_arquivado=0 AND numero is not null";
                break;
            case 'contribuintes':
                $sql .= " AND t.is_recebido=0 AND p.is_arquivado=0 AND numero is null";
                break;
            case 'abertos':
                $sql .= " AND p.is_arquivado=0 AND t.is_recebido=1 AND numero is not null";
                break;
            case 'arquivados':
                $sql .= " AND p.is_arquivado=1 AND numero is not null";
                break;
            case 'vencidos':
                $sql .= " AND p.is_arquivado=0 AND t.data_vencimento<NOW() AND numero is not null";
                break;
            case 'processo-vencido':
                $sql .= " AND p.is_arquivado=0 AND "
                    . self::sqlDataVencimentoAtualizada("p"). " < NOW() AND numero is not null";
                break;
        }
        return parent::getEntityManager()->getConnection()->fetchAll($sql);
    }


    public static function  sqlDataVencimentoAtualizada($tableProcessos): string
    {
        return  " IFNULL("
            . "   ("
            . "         SELECT a.novo_vencimento_processo "
            . "         FROM anexo a "
            . "         WHERE a.processo_id = $tableProcessos.id AND a.novo_vencimento_processo IS NOT NULL"
            . "         ORDER BY a.data DESC LIMIT 1 "
            . "   ), $tableProcessos.data_vencimento "
            . " ) ";
    }

    /**
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    function listarSelect2($busca, $pagina, $disponiveis)
    {
        $total = $this->contar();
        $inicio = ($total * $pagina) - $total;
        $sql = " SELECT p.id,CONCAT(p.numero,'/',p.exercicio) AS text"
            . " FROM App\Model\Processo p"
            . " JOIN p.tramites t WITH p.numeroFase=t.numeroFase AND t.isDespachado=0 "
            . " WHERE p.id IS NOT NULL";
        $aux = explode("/", $busca);
        $numero = (int)$aux[0];
        $exercicio = isset($aux[1]) ? (int)$aux[1] : null;
        $sql .= " AND p.numero like '$numero%'";
        if ($exercicio != null) {
            $sql .= " AND p.exercicio=$exercicio";
        }
        if ($disponiveis) {
            $sql .= " AND t.id NOT IN (SELECT t2.id FROM App\Model\Notificacao n JOIN n.tramite t2)";
        }
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setMaxResults(50);
        $query->setFirstResult($inicio);
        return $query->getResult();
    }

    /**
     * @throws ORMException
     * @throws ReflectionException
     */
    public function listarProcessosPorDataAbertura($dataInicio, $dataFim){
        $sql = " SELECT p FROM App\Model\Processo p "
            . " WHERE  p.id IS NOT NULL ".$this->getFilterDateQuery($dataInicio, $dataFim, 'dataInicio', 'dataFim', "p.dataAbertura");
        $query = parent::getEntityManager()->createQuery($sql);
        $this->setParameteres($query, 'listarProcessosPorDataAbertura', array(), func_get_args());
        return $query->getResult();
    }

    /**
     * @throws ORMException
     * @throws ReflectionException
     */
    function listarQtdeDeTramitesForaDoFluxo($dataInicio, $dataFim, $assunto_id, $interessado_id){
        $sql = "SELECT ("
            . "     SELECT count(t.id) FROM  App\Model\Tramite t "
            . "     WHERE t.processo = p AND t.foraFluxograma=true "
            . "         AND t.setorAtual = i.setor"
            . ") AS qtde_fora_fluxo, p"
            . " FROM App\Model\Processo p "
            . " JOIN p.interessado i"
            . " WHERE p.id > 0 ";
        $sql .= $this->getFilterDateQuery($dataInicio, $dataFim, 'dataInicio', 'dataFim', "p.dataAbertura");
        if($assunto_id){
            $sql .= " AND EXISTS(SELECT t2.id FROM App\Model\Tramite t2 WHERE t2.processo = p AND t2.assunto = :assunto_id) ";
        }
        if($interessado_id){
            $sql .= " AND p.interessado = :interessado_id ";
        }
        $query = parent::getEntityManager()->createQuery($sql);
        $this->setParameteres($query, 'listarQtdeDeTramitesForaDoFluxo', array(), func_get_args());
        return $query->getResult();
    }

    /**
     * @throws ORMException
     * @throws ReflectionException
     */
    public function listarQtdeAgrupada($agrupado, $dataInicio, $dataFim, $limite)
    {
        $sql = " SELECT count(p.id) AS qtde, p as processo"
            . " FROM App\Model\Processo p "
            . " JOIN p.assunto a"
            . " JOIN p.usuarioAbertura u"
            . " WHERE p.id IS NOT NULL";
        $sql .= $this->getFilterDateQuery($dataInicio, $dataFim, 'dataInicio', 'dataFim', "p.dataAbertura");
        $sql .= " GROUP BY p.$agrupado HAVING qtde>0";
        $query = parent::getEntityManager()->createQuery($sql);
        $this->setParameteres($query, 'listarQtdeAgrupada', array('agrupado', 'limite'), func_get_args());
        $query->setMaxResults($limite);
        return $query->getResult();
    }

    /**
     * Busca quantidade de processos por mês.
     * @param $ano
     * @param $assunto_id
     * @return float|int|mixed|string
     * @throws ORMException
     */
    public function buscarQuantidadePorMes($ano, $assunto_id)
    {
        $sql = " SELECT count(p.id) as qtde,SUBSTRING(p.dataAbertura, 6, 2) AS mes"
            . " FROM App\Model\Processo p"
            . " JOIN p.assunto a"
            . " JOIN p.usuarioAbertura u"
            . " WHERE SUBSTRING(p.dataAbertura, 1, 4)=:ano";
        if ($assunto_id != null) {
            $sql .= " AND a.id=$assunto_id";
        }
        $usuario = UsuarioController::getUsuarioLogado();
        if ($usuario != null) {
            if ($usuario->getTipo() == TipoUsuario::USUARIO) {
                $sql .= " AND u.id={$usuario->getId()}";
            }
        }
        $sql .= " GROUP BY mes";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('ano', $ano);
        return $query->getResult();
    }

    /**
     * Listar quantidade de processo por status.
     * @param string $referencia
     * @param $responsavel_id
     * @param $assunto_id
     * @param $interessado_id
     * @return float|int|mixed|string
     * @throws ORMException
     */
    public function listarQtdeProcessos(string $referencia, $responsavel_id, $assunto_id, $interessado_id)
    {
        $sql = " SELECT count(p.id) AS qtde,t"
            . " FROM App\Model\Tramite t "
            . " JOIN t.processo p WITH p.numeroFase=t.numeroFase"
            . " JOIN p.assunto a"
            . " JOIN p.interessado i"
            . " JOIN t.usuarioEnvio ue"
            . " LEFT JOIN t.usuarioRecebimento ur"
            . " JOIN t.setorAtual sa"
            . " WHERE t.isCancelado=false AND p.isArquivado=false";
        switch ($referencia) {
            case 'receber':
                $sql .= " AND t.isRecebido=false";
                if (!empty($responsavel_id)) {
                    $sql .= " AND ue.id=$responsavel_id";
                }
                break;
            case 'aberto':
                $sql .= " AND t.isRecebido=true";
                if (!empty($responsavel_id)) {
                    $sql .= " AND ur.id=$responsavel_id";
                }
                break;
            case 'vencidos':
                $data_atual = Date('Y-m-d');
                $sql .= " AND DATE(t.dataVencimento)<'$data_atual'";
                if (!empty($responsavel_id)) {
                    $sql .= " AND ur.id=$responsavel_id OR ue.id=$responsavel_id";
                }
                break;
        }
        if (!empty($assunto_id)) {
            $sql .= " AND a.id=$assunto_id";
        }
        if (!empty($interessado_id)) {
            $sql .= " AND i.id=$interessado_id";
        }
        $sql .= $this->getCommonFilter();
        $sql .= " GROUP BY t.setorAtual HAVING qtde>0";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @param $exercicio
     * @param $numero_processo
     * @param $origem
     * @param $status_id
     * @param $assunto_id
     * @param $interessado_id
     * @param $setor_origem_id
     * @param $setor_atual_id
     * @param $responsavel_abertura_id
     * @param $data_abertura_ini
     * @param $data_abertura_fim
     * @param $data_arquivamento_ini
     * @param $data_arquivamento_fim
     * @param $data_tramite_ini
     * @param $data_tramite_fim
     * @param $texto
     * @param $tipo_texto
     * @param $ref_texto
     * @return float|int|mixed|string
     * @throws ReflectionException
     * @throws ORMException
     */
    function listarGlobal($exercicio, $numero_processo, $origem, $status_id, $assunto_id, $interessado_id, $setor_origem_id, $setor_atual_id, $responsavel_abertura_id, $data_abertura_ini, $data_abertura_fim, $data_arquivamento_ini, $data_arquivamento_fim, $data_tramite_ini, $data_tramite_fim, $texto, $tipo_texto, $ref_texto)
    {
        $sql = $this->getSqlProcessos($exercicio, $numero_processo, $origem, $status_id, $assunto_id, $interessado_id, $setor_origem_id, $setor_atual_id, $responsavel_abertura_id, $data_abertura_ini, $data_abertura_fim, $data_arquivamento_ini, $data_arquivamento_fim, $data_tramite_ini, $data_tramite_fim, $texto, $ref_texto);
        $query = parent::getEntityManager()->createQuery($sql);
        foreach ($this->getFunctionArgNames('listarGlobal') as $arg) {
            if (${$arg} != null) {
                if (Functions::testDate(${$arg})) {
                    ${$arg} = Functions::converteDataParaMysql(${$arg});
                } else if ($arg == 'texto') {
                    switch ($tipo_texto) {
                        //Contém
                        case 0:
                            ${$arg} = "%" . ${$arg} . "%";
                            break;
                        //Inicia
                        case 1:
                            ${$arg} = ${$arg} . "%";
                            break;
                    }
                }
                if ($arg != 'ref_texto' && $arg != 'tipo_texto') {
                    $query->setParameter($arg, ${$arg});
                }
            }
        }
        return $query->getResult();
    }

    private function getSqlProcessos($exercicio, $numero_processo, $origem, $status_id, $assunto_id, $interessado_id, $setor_origem_id, $setor_atual_id, $responsavel_abertura_id, $data_abertura_ini, $data_abertura_fim, $data_arquivamento_ini, $data_arquivamento_fim, $data_tramite_ini, $data_tramite_fim, $texto, $ref_texto): ?string
    {
        $sql = "SELECT p"
            . " FROM App\Model\Processo p "
            . " JOIN p.tramites t WITH p.numeroFase=t.numeroFase AND t.isCancelado=false"
            . " JOIN t.status st"
            . " JOIN p.assunto a"
            . " JOIN p.interessado i"
            . " JOIN t.setorAtual sat"
            . " JOIN p.setorOrigem so"
            . " LEFT JOIN p.anexos an"
            . " JOIN p.usuarioAbertura ua"
            . " WHERE p.numero IS NOT NULL";
        if ($origem != null) {
            $sql .= " AND p.origem=:origem";
        }
        if ($exercicio != null) {
            $sql .= " AND p.exercicio=:exercicio";
        }
        if ($numero_processo != null) {
            $sql .= " AND p.numero=:numero_processo";
        }
        if ($status_id != null) {
            $sql .= " AND st.id=:status_id";
        }
        if ($assunto_id != null) {
            $sql .= " AND a.id=:assunto_id";
        }
        if ($interessado_id != null) {
            $sql .= " AND i.id=:interessado_id";
        }
        if ($setor_origem_id != null) {
            $sql .= " AND so.id=:setor_origem_id";
        }
        if ($setor_atual_id != null) {
            $sql .= " AND sat.id=:setor_atual_id";
        }
        if ($responsavel_abertura_id != null) {
            $sql .= " AND ua.id=:responsavel_abertura_id";
        }
        $sql .= $this->getFilterDateQuery($data_abertura_ini, $data_abertura_fim, 'data_abertura_ini', 'data_abertura_fim', 'p.dataAbertura');
        $sql .= $this->getFilterDateQuery($data_tramite_ini, $data_tramite_fim, 'data_tramite_ini', 'data_tramite_fim', 't.dataEnvio');
        $sql .= $this->getFilterDateQuery($data_arquivamento_ini, $data_arquivamento_fim, 'data_arquivamento_ini', 'data_arquivamento_fim', 'p.dataArquivamento');
        if ($texto != null) {
            switch ($ref_texto) {
                //Tudo
                case 0:
                    $sql .= " AND (p.objeto LIKE :texto OR t.parecer LIKE :texto OR an.descricao LIKE :texto OR an.textoOCR LIKE :texto)";
                    break;
                //Objeto Processo
                case 1:
                    $sql .= " AND p.objeto LIKE :texto";
                    break;
                //Parecer Trâmite
                case 2:
                    $sql .= " AND t.parecer LIKE :texto";
                    break;
                //Descrição Anexo
                case 3:
                    $sql .= " AND an.descricao LIKE :texto";
                    break;
                //Contéudo Anexo
                case 4:
                    $sql .= " AND an.textoOCR LIKE :texto";
                    break;
            }
        }
        return $sql;
    }

    /**
     * @throws ORMException
     */
    function getExercicios()
    {
        $sql = "SELECT DISTINCT(p.exercicio) as exercicio "
            . " FROM App\Model\Processo p"
            . " ORDER BY exercicio DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * Busca maior número de processo do ano atual
     * @param int|null $exercicio
     * @return int
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    function getMaiorNumero(?int $exercicio = null): ?int
    {
        if (is_null($exercicio)) {
            $exercicio = date('Y');
        }
        $sql = "SELECT MAX(p.numero) numero "
            . " FROM App\Model\Processo p "
            . " WHERE p.exercicio=:ano";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('ano', $exercicio);
        return $query->getSingleScalarResult();
    }


    /**
     * Filtro centralizado comum as listagens de processo do sistema
     * @return string
     * @throws ORMException
     */
    private function getCommonFilter(): ?string
    {
        $exercicio = ProcessoController::getExercicioAtual();
        $common_sql = $exercicio != null ? " AND p.exercicio='$exercicio'" : "";
        $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        if ($usuario != null && $usuario->getTipo() != TipoUsuario::MASTER && count($usuario->getSetores()) > 0) {
            $common_sql .= " AND sa.id IN({$usuario->getSetoresIds(true)})";
        }
        return $common_sql;
    }

    /**
     * @throws ORMException
     */
    public function listarVencidos()
    {
        $data_atual = Date('Y-m-d');
        $sql = "SELECT p"
            . " FROM App\Model\Processo p "
            . " JOIN p.tramites t WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto AND DATE(t.dataVencimento)<'$data_atual'"
            . " JOIN t.setorAtual sa"
            . " WHERE p.isArquivado=false";
        $sql .= $this->getCommonFilter();
        $sql .= " ORDER BY p.dataVencimento ASC";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @return float|int|mixed|string
     * @throws ORMException
     */
    public function listarTramitesVencidos()
    {
        $sql = "SELECT t"
            . " FROM App\Model\Tramite t "
            . " JOIN t.setorAtual sa"
            . " JOIN t.processo p WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto  AND t.isCancelado=false AND t.isDespachado=false"
            . " LEFT JOIN p.anexos an"
            . " WHERE p.isArquivado=false AND t.dataVencimento is not NULL";
        $sql .= $this->getCommonFilter();
        $sql .= " ORDER BY t.dataVencimento ASC";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * Listar quantidade de processo por status.
     * @param $referencia
     * @param $setoAtual
     * @param $assunto
     * @param $interessado
     * @param $responsavel
     * @param $vencimentoIni
     * @param $vencimentoFim
     * @return float|int|mixed|string
     * @throws ORMException
     * @throws ReflectionException
     * @throws ORMException
     */
    public function listarQtdeTramitesVencidos($referencia, $setoAtual, $assunto, $interessado, $responsavel, $vencimentoIni, $vencimentoFim)
    {
        $agrupado = $referencia == 'interessado' || $referencia == 'assunto' ? "p.$referencia" : "t.$referencia";
        $sql = " SELECT count(t.id) AS qtde,t"
            . " FROM App\Model\Tramite t "
            . " JOIN t.setorAtual sa"
            . " JOIN t.processo p WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto AND t.isCancelado=false AND t.isDespachado=false"
            . " WHERE p.isArquivado=false";
        $sql .= $this->getCommonFilter();
        if ($setoAtual != null) {
            $sql .= " AND t.setorAtual=:setorAtual";
        }
        if ($assunto != null) {
            $sql .= " AND p.assunto=:assunto";
        }
        if ($interessado != null) {
            $sql .= " AND p.interessado=:interessado";
        }
        if (!empty($responsavel)) {
            $sql .= " AND t.usuarioEnvio=:responsavel OR t.usuarioRecebimento=:responsavel";
        }
        $sql .= $this->getFilterDateQuery($vencimentoIni, $vencimentoFim, 'vencimentoIni', 'vencimentoFim', "t.dataVencimento");
        $sql .= " GROUP BY $agrupado HAVING qtde>0";
        $query = parent::getEntityManager()->createQuery($sql);
        $this->setParameteres($query, 'listarQtdeTramitesVencidos', array('referencia'), func_get_args());
        return $query->getResult();
    }

    /**
     * @param bool $commom_filter
     * @return float|int|mixed|string
     * @throws ORMException
     */
    function listarArquivados(bool $commom_filter = true)
    {
        $sql = " SELECT t"
            . " FROM App\Model\Tramite t"
            . " JOIN t.setorAtual sa"
            . " JOIN t.processo p  WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto"
            . " WHERE p.isArquivado=true";
        if ($commom_filter) {
            $sql .= $this->getCommonFilter();
        }
        $sql .= " ORDER BY p.dataArquivamento DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @param $commom_filter
     * @return float|int|mixed|string
     * @throws ORMException
     */
    function listarEmAberto($commom_filter)
    {
        $sql = " SELECT t"
            . " FROM App\Model\Tramite t"
            . " JOIN t.processo p WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto AND t.isCancelado=false AND t.isDespachado=false"
            . " JOIN t.setorAtual sa"
            . " WHERE p.isArquivado=false";
        if ($commom_filter) {
            $sql .= $this->getCommonFilter();
        }
        $sql .= " ORDER BY t.dataEnvio DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        //echo $query->getSql();
        return $query->getResult();
    }

    /**
     * @return float|int|mixed|string
     * @throws ORMException
     */
    function listarReceber()
    {
        $sql = " SELECT t"
            . " FROM App\Model\Tramite t"
            . " JOIN t.setorAtual sa"
            . " JOIN t.processo p WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto"
            . " WHERE t.isRecebido=false AND p.isArquivado=false AND t.isCancelado=false";
        $sql .= $this->getCommonFilter();
        $sql .= " ORDER BY t.dataEnvio DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @return float|int|mixed|string
     * @throws ORMException
     */
    function listarEnviados()
    {
        $sql = "SELECT t"
            . " FROM App\Model\Tramite t "
            . " JOIN t.setorAtual sa"
            . " JOIN t.processo p WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto"
            . " WHERE p.isArquivado=false AND t.usuarioEnvio=:usuario_envio";
        $sql .= $this->getCommonFilter();
        $sql .= " ORDER BY t.dataEnvio DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('usuario_envio', UsuarioController::getUsuarioLogadoDoctrine());
        return $query->getResult();
    }

    /**
     * @param $dataIni
     * @param $dataFim
     * @param $setor
     * @param $assunto
     * @param $interessado
     * @return float|int|mixed|string
     * @throws ORMException
     */
    function listarMovimentacao($dataIni, $dataFim, $setor, $assunto, $interessado)
    {
        $sql = " SELECT t"
            . " FROM App\Model\Tramite t"
            . " JOIN t.processo p"
            . " WHERE DATE(t.dataEnvio) BETWEEN :dataIni AND :dataFim";
        if ($setor != null) {
            $sql .= " AND t.setorAtual=:setor";
        }
        if ($assunto != null) {
            $sql .= " AND p.assunto=:assunto";
        }
        if ($interessado != null) {
            $sql .= " AND p.interessado=:interessado";
        }
        $sql .= " ORDER BY t.dataEnvio DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('dataIni', $dataIni);
        $query->setParameter('dataFim', $dataFim);
        if ($setor != null) {
            $query->setParameter('setor', $setor);
        }
        if ($assunto != null) {
            $query->setParameter('assunto', $assunto);
        }
        if ($interessado != null) {
            $query->setParameter('interessado', $interessado);
        }
        return $query->getResult();
    }

    function listarTramites($dataIni, $dataFim, $setor, $assunto, $usuario)
    {
        $sql = " SELECT t"
            . " FROM App\Model\Tramite t"
            . " JOIN t.processo p"
            . " WHERE DATE(t.dataEnvio) BETWEEN :dataIni AND :dataFim";
        if ($setor != null) {
            $sql .= " AND t.setorAtual=:setor";
        }
        if ($assunto != null) {
            $sql .= " AND p.assunto=:assunto";
        }
        if ($usuario != null) {
            $sql .= " AND t.usuarioEnvio=:usuario";
        }
        $sql .= " ORDER BY t.dataEnvio DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('dataIni', $dataIni);
        $query->setParameter('dataFim', $dataFim);
        if ($setor != null) {
            $query->setParameter('setor', $setor);
        }
        if ($assunto != null) {
            $query->setParameter('assunto', $assunto);
        }
        if ($usuario != null) {
            $query->setParameter('usuario', $usuario);
        }
        return $query->getResult();
    }

    /**
     * @param $dataIni
     * @param $dataFim
     * @return float|int|mixed|string
     * @throws ORMException
     */
    function listarVencimentoProximos($dataIni, $dataFim){
        $sql = "SELECT p FROM \App\Model\Processo p "
            . " JOIN p.tramites t WITH p.numeroFase=t.numeroFase AND t.assunto=p.assunto"
            . " JOIN t.setorAtual sa"
            . " WHERE p.isArquivado=false AND p.dataVencimento BETWEEN :dataIni AND :dataFim ";
        $sql .= $this->getCommonFilter();

        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('dataIni', $dataIni);
        $query->setParameter('dataFim', $dataFim);
        return $query->getResult();
    }

    /**
     * @param $processoId
     * @return array
     * @throws ORMException
     */
    function buscarLxSignIdDosAnexos($processoId): ?array
    {
        $sql = "SELECT ASS.lxsign_id AS id FROM assinatura ASS "
            . "INNER JOIN anexo AN "
            . "ON AN.id = ASS.anexo_id "
            . "INNER JOIN processo PRO "
            . "ON PRO.id = AN.processo_id "
            . "WHERE PRO.id = :processo_id";
        $rsm = new ResultSetMappingBuilder(parent::getEntityManager());
        $rsm->addScalarResult('id', 'id');
        $query = parent::getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('processo_id', $processoId);
        $result = $query->getScalarResult();
        return array_map(function ($item){
            return $item['id'];
        }, $result);
    }
}
