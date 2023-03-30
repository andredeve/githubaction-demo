<?php

namespace App\Model\Dao;

use App\Controller\ProcessoController;
use App\Enum\StatusSolicitacao;
use App\Model\Anexo;
use App\Model\Componente;
use App\Model\Solicitacao;
use Core\Exception\BusinessException;
use Core\Model\AppDao;
use Core\Util\Functions;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\TransactionRequiredException;
use Exception;
use ReflectionException;

class AnexoDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Anexo::class)
    {
        parent::__construct($entidade);
    }
    
    function inserir($object = null, bool $validarSomenteLeitura = true): ?int {
        try {
            $retorno = parent::inserir($object, $validarSomenteLeitura);
            $this->clear();
        } catch (Exception $e) {
            error_log($e->getMessage());
            error_log($e->getTraceAsString());
        }
        return !isset($retorno) ? null : $retorno;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function listarSemOCR($ano)
    {
        $sql = "SELECT a FROM App\Model\Anexo a WHERE a.arquivo IS NOT NULL AND a.textoOCR IS NULL";
        if ($ano != null) {
            $sql .= " AND a.exercicio=$ano";
        }
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws \Doctrine\DBAL\Exception
     */
    function listarComArquivos($ano, $legado)
    {
        $sql = "SELECT a FROM App\Model\Anexo a JOIN a.processo p WHERE a.arquivo IS NOT NULL";
        if ($legado != null) {
            $sql .= " AND p.legado='$legado'";
        }
        if ($ano != null) {
            $sql .= " AND p.exercicio=$ano";
        }
        $sql .= " ORDER BY a.data DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws ReflectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function listarQtde($group_by, $tipo_documento_id, $data_ini, $data_fim, $usuario_id)
    {
        $sql = " SELECT count(a.id) AS qtde, SUM(a.qtdePaginas) as totalPaginas, a "
            . " FROM App\Model\Anexo a "
            . " JOIN a.processo p"
            . " JOIN a.usuario u"
            . " JOIN a.tipo t"
            . " WHERE a.arquivo IS NOT NULL";
        $sql .= $this->getCommonFilter();
        if ($tipo_documento_id != null) {
            $sql .= " AND t.id=:tipo_documento_id";
        }
        if ($usuario_id != null) {
            $sql .= " AND u.id=:usuario_id";
        }
        $sql .= $this->getFilterDateQuery($data_ini, $data_fim, "data_ini", "data_fim", "a.dataCadastro");
        $sql .= " GROUP BY a.$group_by HAVING qtde>0";
        $query = parent::getEntityManager()->createQuery($sql);
        foreach ($this->getFunctionArgNames('listarQtde') as $arg) {
            if (${$arg} != null && $arg != 'group_by') {
                if (Functions::testDate(${$arg})) {
                    ${$arg} = Functions::converteDataParaMysql(${$arg});
                }
                $query->setParameter($arg, ${$arg});
            }
        }
        return $query->getResult();
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws ReflectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function listarAnexos($tipo_documento_id, $data_ini, $data_fim, $usuario_id)
    {
        $sql = "SELECT a,p"
            . " FROM App\Model\Anexo a "
            . " JOIN a.processo p"
            . " JOIN a.usuario u"
            . " JOIN a.tipo t"
            . " WHERE a.arquivo IS NOT NULL";
        $sql .= $this->getCommonFilter();
        if ($tipo_documento_id != null) {
            $sql .= " AND t.id=:tipo_documento_id";
        }
        if ($usuario_id != null) {
            $sql .= " AND u.id=:usuario_id";
        }
        $sql .= $this->getFilterDateQuery($data_ini, $data_fim, "data_ini", "data_fim", "a.dataCadastro");
        $sql .= " ORDER BY t.dataCadastro ASC";
        $query = parent::getEntityManager()->createQuery($sql);
        foreach ($this->getFunctionArgNames('listarAnexos') as $arg) {
            if (${$arg} != null) {
                if (Functions::testDate(${$arg})) {
                    ${$arg} = Functions::converteDataParaMysql(${$arg});
                }
                $query->setParameter($arg, ${$arg});
            }
        }
        return $query->getResult();
    }

    /**
     * Filtro centralizado comum as listagens de processo do sistema
     * @return string
     */
    private function getCommonFilter(): ?string
    {
        $exercicio = ProcessoController::getExercicioAtual();
        return $exercicio != null ? " AND a.exercicio='$exercicio'" : "";
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws BusinessException
     */
    function remover(?int $id = null, $entity = null) {
        if (is_null($id) && is_null($entity)) {
            throw new BusinessException("Informa o id ou a entidade que deseja remover.");
        }
        if (is_null($entity)) {
            $entity = $this->buscar($id);
        }
        /**
         * @var Componente $componente
         */
        $componente = (new Componente())->buscarPorCampos(array("anexo" => $entity));
        if(!empty($componente)){
            $componente->remover();
        }
        parent::remover(null, $entity);
    }

    /**
     * @param int $anexo_id
     * @param int $tramite_validacao_id
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function isRequiredAttach(int $anexo_id, int $tramite_validacao_id): bool {
        $sql = "SELECT COUNT(id) AS amount FROM documento_requerido WHERE anexo_id = :anexo AND tramite_validacao_id = :tramite";
        $rsm = new ResultSetMappingBuilder(self::getEntityManager());
        $rsm->addScalarResult("amount", "amount");
        $query = self::getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter("anexo", $anexo_id);
        $query->setParameter("tramite", $tramite_validacao_id);
        return intval($query->getSingleScalarResult()) > 0;
    }
}
