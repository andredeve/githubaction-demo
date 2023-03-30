<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Fluxograma;
use Core\Model\AppDao;

class FluxogramaDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Fluxograma::class) {
        parent::__construct($entidade);
    }
}