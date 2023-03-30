<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\TipoAnexo;
use Core\Model\AppDao;

class TipoAnexoDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = TipoAnexo::class) {
        parent::__construct($entidade);
    }
}