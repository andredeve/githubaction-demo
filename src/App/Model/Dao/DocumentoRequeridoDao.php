<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\Anexo;
use App\Model\DocumentoRequerido;
use App\Model\Tramite;
use Core\Model\AppDao;
use Doctrine\ORM\ORMException;

class DocumentoRequeridoDao extends AppDao
{

    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = DocumentoRequerido::class)
    {
        parent::__construct($entidade);
    }
}
