<?php

namespace App\Log;

use App\Controller\UsuarioController;
use App\Enum\TipoHistoricoAnexo;
use App\Enum\TipoLog;
use App\Model\Anexo;
use App\Model\Log;
use App\Model\Usuario;
use Core\Exception\BusinessException;
use Core\Util\DateUtil;
use Core\Util\EntityManagerConn;
use Core\Util\Functions;
use DateTime;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Exception;

// TODO: Migrar dados e apagar entidade do banco de dados MySQL.
class HistoricoAnexo {

    private static function registrarLogCriacao(Anexo $anexo, ?Usuario $usuarioLog) {
        $registro = new AttachLogger();
        if (is_null($usuarioLog)) {
            $registro->setUserName("Sistema Externo");
        } else {
            $registro->setUserName($usuarioLog->getPessoa()->getNome());
            $registro->setUserCod($usuarioLog->getId());
        }
        $registro->setAttachCod($anexo->getId());
        $registro->setIp(Functions::getUserIp());
        $registro->setFile($anexo->getArquivo(false, false, true));
        $registro->setDate($anexo->getDataCadastro());
        $registro->registerCreate();
    }

    private static function registrarLogAtualizacao(Anexo $anexoOld, ?Anexo $anexoNew, ?string $motivo, ?string $observacao, ?Usuario $usuarioLog) {
        $registro = new AttachLogger();
        if (is_null($usuarioLog)) {
            $registro->setUserName("Sistema Externo");
        } else {
            $registro->setUserName($usuarioLog->getPessoa()->getNome());
            $registro->setUserCod($usuarioLog->getId());
        }
        if (!is_null($anexoNew)) {
            $registro->setAttachCod($anexoNew->getId());
            $registro->setFile($anexoNew->getArquivo(false, false, true));
        }
        $registro->setAttachCodOld($anexoOld->getId());
        $registro->setIp(Functions::getUserIp());
        $registro->setFileOld($anexoOld->getArquivo(false, false, true));
        $registro->setDate(new DateTime());
        $registro->setMotive($motivo);
        $registro->setObservation($observacao);
        $registro->registerUpdate();
    }

    private static function registrarLogDelecao(Anexo $anexo, ?string $motivo, ?string $observacao, ?Usuario $usuarioLog) {
        $registro = new AttachLogger();
        if (is_null($usuarioLog)) {
            $registro->setUserName("Sistema Externo");
        } else {
            $registro->setUserName($usuarioLog->getPessoa()->getNome());
            $registro->setUserCod($usuarioLog->getId());
        }
        $registro->setAttachCod($anexo->getId());
        $registro->setProcess($anexo->getProcesso()->getNumero() . "/" . $anexo->getProcesso()->getExercicio());
        $registro->setNumber($anexo->getNumero() . "/" . $anexo->getExercicio());
        $registro->setIp(Functions::getUserIp());
        $registro->setFile($anexo->getArquivoOriginal());
        $registro->setDate($anexo->getDataCadastro());
        $registro->setMotive($motivo);
        $registro->setObservation($observacao);
        $registro->registerDelete();
    }

    /**
     * @param $tipo
     * @param string|null $motivo
     * @param string|null $observacacao
     * @param Anexo|null $antigo
     * @param Anexo|null $novo
     * @param Usuario|null $usuario
     * @throws BusinessException
     */
    static function registrar($tipo, ?string $motivo, ?string $observacacao, ?Anexo $antigo = null, ?Anexo $novo = null, ?Usuario $usuario = null) {
        if (is_null($usuario)) {
            $usuario = UsuarioController::getUsuarioLogadoDoctrine();
        }
        switch ($tipo) {
            case TipoHistoricoAnexo::INSERT: self::registrarLogCriacao($novo, $usuario);
                break;
            case TipoHistoricoAnexo::UPDATE: self::registrarLogAtualizacao($antigo, $novo, $motivo, $observacacao, $usuario);
                break;
            case TipoHistoricoAnexo::DELETE: {
                    if (is_null($antigo)) {
                        throw new BusinessException("Informe a entidade de " . Anexo::class . " que foi removida para ser registrada no log.");
                    }
                    self::registrarLogDelecao($antigo, $observacacao, null, $usuario);
                    break;
                }
            default: Functions::escreverLogErro("Caso para tipo de log de anexo não definido: $tipo.");
        }
    }

    /**
     * @throws Exception
     */
    static function registrarLogAnexoRemovido(Anexo $anexo, ?string $motivo, ?string $observacao, ?Usuario $usuario) {
        self::registrarLogDelecao($anexo, $motivo, $observacao, $usuario);
    }

    /**
     * @param Anexo $anexo
     * @return array
     */
    public static function historico(Anexo $anexo): array {
        $historico = array();
        $log = AttachLogger::getLog($anexo->getId());
        if (!empty($log['create'])) {
            foreach ($log['create'] as $item) {
                $historicoItem['id'] = $item['id'] . 1;
                $historicoItem['usuario'] = $item['user_name'];
                $historicoItem['data'] = DateUtil::timestampToDate(intval($item['date']))->format("d/m/Y - H:i");
                $historicoItem['mensagem'] = "Anexo adicionado por {$historicoItem['usuario']}.";
                $historico[] = $historicoItem;
            }
        }
        if (!empty($log['update'])) {
            foreach ($log['update'] as $item) {
                $historicoItem['id'] = $item['id'] . 1;
                $historicoItem['usuario'] = $item['user_name'];
                $historicoItem['data'] = DateUtil::timestampToDate(intval($item['date']))->format("d/m/Y - H:i");
                $historicoItem['mensagem'] = "Anexo atualizado por {$historicoItem['usuario']}. {$item['observation']}";
                $historico[] = $historicoItem;
            }
        }
        if (!empty($log['delete'])) {
            foreach ($log['delete'] as $item) {
                $historicoItem['id'] = $item['id'] . 1;
                $historicoItem['usuario'] = $item['user_name'];
                $historicoItem['data'] = DateUtil::timestampToDate(intval($item['date']))->format("d/m/Y - H:i");
                $historicoItem['mensagem'] = "Anexo removido por {$historicoItem['usuario']}. {$item['observation']}";
                $historico[] = $historicoItem;
            }
        }
        return $historico;
    }

    /**
     * @param Anexo $anexo
     * @return array
     * @throws ORMException
     * @throws \Doctrine\DBAL\Exception
     */
    public static function historicoLegado(Anexo $anexo): array {
        $id_param = $anexo->getId();
        $numero_param = "%numero = {$anexo->getNumero()}%";
        $exercicio_param = "%exercicio = {$anexo->getNumero()}%";
        $tipo_param = "%tipo = {$anexo->getTipo()->getDescricao()}%";
        $processo_param = "%processo = {$anexo->getProcesso()->getNumero()}/{$anexo->getProcesso()->getExercicio()}%";
        $em = EntityManagerConn::getEntityManager();
        $sql = "SELECT l.id, l.nome_usuario, l.horario, IF(tipo like 'update', CONCAT('Anexo atualizado por ', l.nome_usuario, '.'), CONCAT('Anexo adicionado por ', l.nome_usuario, '.')) AS mensagem, l.mensagem AS complemento 
            FROM log l 
            WHERE 
                tabela LIKE 'anexo' 
                AND (
                    l.anexo_id = :id_param ";
        if (!is_null($anexo->getNumero()) && $anexo->getNumero() != "") {
            $sql .= "OR (
                        l.antigo LIKE :numero_param 
                        AND l.antigo LIKE :exercicio_param 
                        AND l.antigo LIKE :tipo_param                        
                        AND l.antigo LIKE :processo_param                        
                    )
                    OR (
                        l.novo LIKE :numero_param
                        AND l.novo LIKE :exercicio_param 
                        AND l.novo LIKE :tipo_param
                        AND l.antigo LIKE :processo_param 
                    )";
        }
        $sql .= ") ORDER BY id DESC";
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult("id", "id");
        $rsm->addScalarResult("nome_usuario", "usuario");
        $rsm->addScalarResult("horario", "data");
        $rsm->addScalarResult("mensagem", "mensagem");
        $rsm->addScalarResult("complemento", "complemento");
        $query = $em->createNativeQuery($sql, $rsm);
        $query->setParameter("id_param", $id_param, \PDO::PARAM_INT);
        $query->setParameter("numero_param", $numero_param);
        $query->setParameter("exercicio_param", $exercicio_param);
        $query->setParameter("tipo_param", $tipo_param);
        $query->setParameter("processo_param", $processo_param);
        return $query->getScalarResult();
    }
}
