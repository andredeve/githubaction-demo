<?php

use Core\Controller\AppController;
use Core\Util\Functions;

require_once __DIR__ . "/../../../bootstrap.php";

$file = APP_PATH . "src/Tests/pdf/teste_conversao.pdf";
Functions::adicionarPaginacaoECarimbo($file, AppController::getClienteConfig(), 0, false, "I");