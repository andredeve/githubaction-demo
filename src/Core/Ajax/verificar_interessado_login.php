<?php

use App\Controller\InteressadoController;

include '../../../bootstrap.php';
echo InteressadoController::isLogado() ? 1 : 0;
