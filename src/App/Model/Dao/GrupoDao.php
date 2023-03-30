<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Grupo;
use Core\Model\AppDao;

class GrupoDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Grupo::class) {
        parent::__construct($entidade);
    }
}
