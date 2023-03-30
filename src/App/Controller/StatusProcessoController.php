<?php

namespace App\Controller;

use App\Model\StatusProcesso;
use Core\Controller\AppController;

/**
 * Classe StatusProcessoController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   09/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class StatusProcessoController extends AppController {

    function __construct() {
        parent::__construct(get_class());
    }

    function inserir() {
        $_POST['id'] = count((new StatusProcesso())->listar()) + 1;
        return parent::inserir();
    }

}