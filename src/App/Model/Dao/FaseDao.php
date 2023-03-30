<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Fase;
use Core\Model\AppDao;

class FaseDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Fase::class) {
        parent::__construct($entidade);
    }

}
