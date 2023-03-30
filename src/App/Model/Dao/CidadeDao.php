<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Cidade;
use Core\Model\AppDao;

class CidadeDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Cidade::class) {
        parent::__construct($entidade);
    }
}
