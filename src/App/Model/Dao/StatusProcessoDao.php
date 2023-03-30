<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\StatusProcesso;
use Core\Model\AppDao;

class StatusProcessoDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = StatusProcesso::class) {
        parent::__construct($entidade);
    }

}
