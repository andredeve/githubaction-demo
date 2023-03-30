<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Pergunta;
use Core\Model\AppDao;

class PerguntaDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Pergunta::class) {
        parent::__construct($entidade);
    }

}
