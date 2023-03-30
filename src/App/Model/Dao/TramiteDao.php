<?php

namespace App\Model\Dao;

use App\Model\Tramite;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;

class TramiteDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Tramite::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @param $dataIni
     * @param $dataFim
     * @param $setorOrigemId
     * @param $responsavelOrigemId
     * @param $setorDestinoId
     * @param $responsavelDestinoId
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function buscarRemessa($dataIni, $dataFim, $setorOrigemId, $responsavelOrigemId, $setorDestinoId, $responsavelDestinoId)
    {
        $sql = "SELECT t"
            . " FROM App\Model\Tramite t"
            . " JOIN t.setorAnterior sant"
            . " JOIN t.setorAtual sa"
            . " JOIN t.usuarioEnvio u"
            . " LEFT JOIN t.usuarioDestino ud"
            . " WHERE t.isRecebido=false AND t.remessa IS NULL"
            . " AND DATE(t.dataEnvio) BETWEEN :dataIni AND :dataFim"
            . " AND sant.id=:setorOrigemId AND u.id=:responsavelOrigemId"
            . " AND sa.id=:setorDestinoId";
        if ($responsavelDestinoId != null) {
            $sql .= " AND ud.id=$responsavelDestinoId";
        }
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('dataIni', $dataIni);
        $query->setParameter('dataFim', $dataFim);
        $query->setParameter('setorOrigemId', $setorOrigemId);
        $query->setParameter('responsavelOrigemId', $responsavelOrigemId);
        $query->setParameter('setorDestinoId', $setorDestinoId);
        return $query->getResult();
    }

    /**
     * Método lista todos os tramites feito para o usuário e setor informados.
     * @param $usuario_id
     * @param $setor_id
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function listarTramitesNaoRecebidos($usuario_id, $setor_id){
        $sql = "SELECT t "
                . " FROM App\Model\Tramite t "
                . " JOIN t.processo p"
                . " WHERE t.isRecebido=false "
                . " AND p.isArquivado=false AND t.isCancelado=false "
                . " AND t.usuarioDestino=:usuarioDestinoId "
                . " AND t.setorAtual = :setorAtualId ";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('usuarioDestinoId', $usuario_id);
        $query->setParameter('setorAtualId', $setor_id);
        return $query->getResult();
    }

    /**
     * @param $tramiteId
     * @return array
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function buscarLxSignIdDosAnexos($tramiteId): ?array
    {
        $sql = "SELECT ASS.lxsign_id AS id FROM assinatura ASS "
            . "INNER JOIN anexo AN "
            . "ON AN.id = ASS.anexo_id "
            . "INNER JOIN processo PRO "
            . "ON PRO.id = AN.processo_id "
            . "INNER JOIN tramite TRA "
            . "ON TRA.processo_id = PRO.id "
            . "WHERE TRA.id = :tramite_id";
		$rsm = new ResultSetMappingBuilder(parent::getEntityManager());
		$rsm->addScalarResult('id', 'id');
        $query = parent::getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('tramite_id', $tramiteId);
        $result = $query->getScalarResult();
		return array_map(function ($item){
			return $item['id'];
		}, $result);
    }


    /**
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function getSetoresId(int $processo_id){
        $sql = "
            SELECT setor_anterior_id as id 
              FROM `tramite`
             WHERE processo_id = :processo_id
               AND is_cancelado = :is_cancelado
            UNION
            SELECT setor_atual_id as id 
              FROM `tramite` 
             WHERE processo_id= :processo_id
               AND is_cancelado = :is_cancelado";

        $rsm = new ResultSetMappingBuilder(self::getEntityManager());
        $rsm->addScalarResult('id', 'id', "integer");

        $query = self::getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('processo_id', $processo_id);
        $query->setParameter('is_cancelado', 0);

        $result = $query->getScalarResult();
        return array_map(function ($item){
            return intval($item['id']);
        }, $result);
    }
}
