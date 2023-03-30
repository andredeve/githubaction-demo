<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Converter;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

class ConverterDao extends AppDao
{

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Converter::class)
    {
        parent::__construct($entidade);
    }

    /**
     * @throws ORMException
     * @throws Exception
     * @throws \Doctrine\ORM\ORMException
     */
    function listarNaoIniciada(){
        $sql = " SELECT c FROM App\Model\Converter c
         WHERE c.dataInicio IS NULL ";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }

}

