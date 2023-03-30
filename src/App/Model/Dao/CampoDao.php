<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Campo;
use Core\Model\AppDao;

class CampoDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Campo::class) {
        parent::__construct($entidade);
    }

}
