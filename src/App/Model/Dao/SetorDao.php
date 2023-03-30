<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Setor;
use Core\Model\AppDao;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;

class SetorDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Setor::class) {
        parent::__construct($entidade);
    }

    /**
     * Busca setores pelo início do seu nome
     * @param $descricao
     * @return array
     * @throws \Exception
     * @throws \Doctrine\ORM\ORMException
     */
    public function buscarPorDescricao($descricao) {
        $sql = "SELECT s FROM \App\Model\Setor s WHERE s.nome LIKE '%$descricao%'";
        $query = parent::getEntityManager()->createQuery($sql);
        return $query->getResult();
    }
}
