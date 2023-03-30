<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Classificacao;
use Core\Model\AppDao;

class ClassificacaoDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Classificacao::class) {
        parent::__construct($entidade);
    }

}
