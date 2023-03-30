<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\TipoLocal;
use Core\Model\AppDao;

class TipoLocalDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = TipoLocal::class)
    {
        parent::__construct($entidade);
    }
}