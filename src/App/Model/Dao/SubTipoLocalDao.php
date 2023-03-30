<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\SubTipoLocal;
use Core\Model\AppDao;

class SubTipoLocalDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = SubTipoLocal::class)
    {
        parent::__construct($entidade);
    }
}