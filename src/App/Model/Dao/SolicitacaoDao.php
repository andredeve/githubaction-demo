<?php

namespace App\Model\Dao;

use App\Enum\StatusSolicitacao;
use App\Enum\TipoSolicitacao;
use App\Model\Solicitacao;
use App\Model\AnexoAlteracao;
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

class SolicitacaoDao extends AppDao
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
        parent::__construct(new Solicitacao(), $order_by);
        $this->em = parent::getEntityManager();
    }

    /**
     * @param Solicitacao $solicitacao
     * @return float|int|mixed|string
     */
    public function aprovar(Solicitacao $solicitacao) {
        return $this->em->createQueryBuilder()
            ->update(Solicitacao::class, "s")
            ->set("s.status", "'Aprovado'")
            ->set("s.modificadoEm", "'" . (new DateTime())->format('Y-m-d H:i:s') . "'")
            ->where("s.id = {$solicitacao->getId()}")
            ->getQuery()->execute();
    }

    /**
     * @param $id
     * @return float|int|mixed|string
     */
    public function reprovar($id) {
        return $this->em->createQueryBuilder()
            ->update(Solicitacao::class, "s")
            ->set("s.status", "'Recusado'")
            ->set("s.modificadoEm", "'" . (new DateTime())->format('Y-m-d H:i:s') . "'")
            ->where("s.id = $id")
            ->getQuery()->execute();
    }

    /**
     * @param int $solicitacaoId Id da solicitação que deseja encontrar o número do processo.
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function buscarProcessoId(int $solicitacaoId): ?int
    {
        $sql = "SELECT a.processo_id AS processo_id FROM solicitacao s INNER JOIN anexo a ON s.anexo_anterior_id = a.id WHERE s.id = :id";
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('processo_id', 'processo_id');
        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter("id", $solicitacaoId);
        return intval($query->getSingleScalarResult());
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function buscarQuantidade(): ?int
    {
        $sql = "SELECT COUNT(id) AS qtde FROM solicitacao WHERE status LIKE 'Pendente'";
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('qtde', 'qtde');
        $query = $this->em->createNativeQuery($sql, $rsm);
        return  intval($query->getSingleScalarResult());
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function possuiPendencia($anexo_id, $tipo): ?bool
    {
        $sql = "SELECT COUNT(id) AS qtde FROM solicitacao WHERE anexo_anterior_id = :anexo_id AND status = 'Pendente' AND tipo LIKE :tipo";
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult('qtde', 'qtde');
        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter("anexo_id", $anexo_id);
        $query->setParameter("tipo", $tipo);
        return  intval($query->getSingleScalarResult()) > 0;
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
        return self::getEntityManager()->find(Solicitacao::class, $id);
    }

    /**
     * @throws ORMException
     */
    public function removerPendenciasPorAnexo(int $anexo_id) {
        $sql = "DELETE FROM solicitacao WHERE (anexo_anterior_id = :anexo_anterior_id OR anexo_novo_id = :anexo_novo_id) AND status LIKE :status";
        $query = parent::getEntityManager()->getConnection()->prepare($sql);
        $query->bindValue("anexo_anterior_id", $anexo_id, \PDO::PARAM_INT);
        $query->bindValue("anexo_novo_id", $anexo_id, \PDO::PARAM_INT);
        $query->bindValue("status", StatusSolicitacao::Pendente, \PDO::PARAM_STR);
        $query->execute();
    }

    /**
     * @throws ORMException
     */
    public function procurarSolicitacaoEdicaoPendente(int $anexo_id): ?Solicitacao {
        try {
            $query = self::getEntityManager()->createQueryBuilder()
                ->select("s")
                ->from(Solicitacao::class, "s")
                ->where("s.anexoAnterior = :anexo_id AND s.status LIKE :status AND s.tipo LIKE :tipo");
            $query->setParameter("anexo_id", $anexo_id);
            $query->setParameter("status", StatusSolicitacao::Pendente);
            $query->setParameter("tipo", TipoSolicitacao::Edicao);
            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            Functions::escreverLogErro($e);
            return null;
        }
    }
    
    /**
     * 
     * @param Solicitacao $solicitacao
     * @return AnexoAlteracao|null
     */
    public function procurarAnexoSolicitacaoEdicaoPendente(Solicitacao $solicitacao): ?AnexoAlteracao {
        try {
            if(!$solicitacao->getAnexoNovo()){
                return null;
            }
            $query = self::getEntityManager()->createQueryBuilder()
                ->select("s")
                ->from(AnexoAlteracao::class, "s")
                ->where("s.id = :anexo_id");
            $query->setParameter("anexo_id", $solicitacao->getAnexoNovo()->getId());
            return $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            Functions::escreverLogErro($e);
            return null;
        }
    }
}