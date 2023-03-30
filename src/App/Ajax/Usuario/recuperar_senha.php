<?php

use App\Controller\UsuarioController;

require_once '../../../../bootstrap.php';
$usuarioController = new UsuarioController();
$usuarioController->recuperarSenha();

