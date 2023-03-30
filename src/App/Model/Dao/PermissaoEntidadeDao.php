<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\PermissaoEntidade;
use Core\Model\AppDao;

class PermissaoEntidadeDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = PermissaoEntidade::class) {
        parent::__construct($entidade);
    }

}
