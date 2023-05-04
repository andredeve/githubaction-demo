<?php

namespace App\Model\Dao;

use App\Model\Substituicao;
use Core\Model\AppDao;
use Core\Util\Functions;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\TransactionRequiredException;

class SubstituicaoDao extends AppDao
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @param $order_by
     * @throws ORMException
     */
    public function __construct($order_by = null)
    {
        parent::__construct(new Substituicao(), $order_by);
        $this->em = parent::getEntityManager();
    }

    /**
     * @param int $substituicaoId Id da solicitação que deseja encontrar o número do processo.
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function buscarProcessoId(int $substituicaoId): ?int
    {
        $sql = "SELECT a.processo_id AS processo_id FROM substituicao s INNER JOIN anexo a ON s.anexo_anterior_id = a.id WHERE s.id = :id";
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('processo_id', 'processo_id');
        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $substituicaoId);
        return intval($query->getSingleScalarResult());
    }

    /**
     * Metodo que busca um registro no banco de dados
     * @param int $id
     * @return object|null
     * @return mixed
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ORMException
     */
    public function buscarSubstituicoesAnexo(int $anexo_id)
    {
        $sql = "SELECT s FROM App\Model\Substituicao s WHERE s.anexo = '$anexo_id'";
        // $sql = "SELECT a FROM App\Model\AnexoSubstituicao a INNER JOIN App\Model\Substituicao s ON s.anexo_anterior_id = a.id WHERE s.anexo_id = '$anexo_id'";
        $sql .= " ORDER BY s.anexo DESC, s.id DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

    /**
     * Metodo que busca um registro no banco de dados
     * @param int $id
     * @return object|null
     * @return mixed
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ORMException
     */
    public static function get(int $id)
    {
        return self::getEntityManager()->find(Substituicao::class, $id);
    }
}