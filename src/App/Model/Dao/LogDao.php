<?php

namespace App\Model\Dao;

use App\Model\Log;
use Core\Model\AppDao;

class LogDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Log::class) {
        parent::__construct($entidade);
    }

}
