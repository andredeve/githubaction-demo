<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Local;
use Core\Model\AppDao;

class LocalDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Local::class)
    {
        parent::__construct($entidade);
    }
}