<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Anexo;
use App\Model\Assinatura;
use Core\Model\AppDao;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class AssinaturaDao extends AppDao
{
    private $em;

    /**
     * @throws ORMException
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Assinatura::class)
    {
        parent::__construct($entidade);
        $this->em = parent::getEntityManager();
    }

    /**
     * @throws NonUniqueResultException
     */
    function getLxSignId(int $anexo_id): ?int {
        $sql = "SELECT lxsign_id FROM assinatura WHERE anexo_id = $anexo_id";
        $rsm = new ResultSetMappingBuilder($this->em);
        $rsm->addScalarResult("lxsign_id", "lxsign_id");
        try {
            $val = intval($this->em->createNativeQuery($sql, $rsm)->getSingleScalarResult());
            return $val > 0 ? $val : null;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
