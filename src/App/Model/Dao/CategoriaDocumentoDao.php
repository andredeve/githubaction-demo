<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\CategoriaDocumento;
use Core\Model\AppDao;

class CategoriaDocumentoDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = CategoriaDocumento::class)
    {
        parent::__construct($entidade);
    }
}