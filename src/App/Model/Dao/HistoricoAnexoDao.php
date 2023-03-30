<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Log\HistoricoAnexo;

class HistoricoAnexoDao extends LogDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = HistoricoAnexo::class)
    {
        parent::__construct($entidade);
    }

}
