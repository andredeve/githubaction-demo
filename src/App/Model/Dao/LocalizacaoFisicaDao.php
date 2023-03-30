<?php /** @noinspection PhpUnused */

namespace App\Model\Dao;

use App\Model\LocalizacaoFisica;
use Core\Model\AppDao;

class LocalizacaoFisicaDao extends AppDao
{
    /**
     * @deprecated Utilizar construtor vazio.
     */
    function __construct($entidade = LocalizacaoFisica::class)
    {
        parent::__construct($entidade);
    }
}