<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Util\GuiaRemessa;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

class GuiaRemessaDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = GuiaRemessa::class) {
        parent::__construct($entidade);
    }

    /**
     * Busca maior número de processo do ano atual.
     * @return float|int|mixed|string
     * @throws Exception
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    function getMaiorNumero() {
        $sql = "SELECT MAX(r.numero) numero "
                . " FROM App\Model\Remessa r "
                . " WHERE r.exercicio=:ano";
        $query = parent::getEntityManager()->createQuery($sql);
        $query->setParameter('ano', Date('Y'));
        return $query->getResult();
    }

}
