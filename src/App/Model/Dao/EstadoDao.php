<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Estado;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

class EstadoDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Estado::class) {
        parent::__construct($entidade);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws ORMException
     * @throws Exception
     */
    function getCidades($estado): ?array
    {
        return parent::getEntityManager()->getRepository('App\Model\Cidade')->findBy(array('estado' => $estado), array('nome' => 'ASC'));
    }
}
