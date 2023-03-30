<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Endereco;
use Core\Model\AppDao;

class EnderecoDao extends AppDao {

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Endereco::class) {
        parent::__construct($entidade);
    }

}
