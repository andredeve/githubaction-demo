<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Remessa;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

class RemessaDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Remessa::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @param $remessa
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function getProcessos($remessa)
    {
        $sql = "SELECT p 
            FROM \App\Model\Processo p 
            JOIN \App\Model\Tramite t WITH p=t.processo 
            JOIN \App\Model\Remessa r WITH r=t.remessa
            WHERE r=:remessa";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('remessa', $remessa);
        return $query->getResult();
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function listar()
    {
        $sql = "SELECT r FROM \App\Model\Remessa r JOIN \App\Model\Tramite t WITH t.remessa=r";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }
}