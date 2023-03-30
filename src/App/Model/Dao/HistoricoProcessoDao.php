<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\HistoricoProcesso;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

class HistoricoProcessoDao extends AppDao
{

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = HistoricoProcesso::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     */
    function listarProximos($limite)
    {
        $sql = " SELECT h FROM \App\Model\HistoricoProcesso h "
                 . "  ORDER BY h.horario DESC";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setMaxResults($limite);
        $query->setFirstResult($limite);
        return $query->getResult();
    }
}
