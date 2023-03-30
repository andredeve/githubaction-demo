<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Tarefa;
use Core\Model\AppDao;

class TarefaDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Tarefa::class) {
        parent::__construct($entidade);
    }

}
