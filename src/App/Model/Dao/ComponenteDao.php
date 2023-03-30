<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Componente;
use Core\Model\AppDao;

class ComponenteDao extends AppDao
{

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = Componente::class)
    {
        parent::__construct($entidade);
    }

    

}
