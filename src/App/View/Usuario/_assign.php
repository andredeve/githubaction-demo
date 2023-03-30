<?php

use App\Controller\UsuarioController;
use App\Model\Estado;
use App\Model\Grupo;
use App\Model\Setor;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//$smarty->assign("empresas", (new Empresa())->listar());
$smarty->assign("tipo_options", UsuarioController::getTipos());
$smarty->assign("grupos", (new Grupo())->listar());
$smarty->assign("estados",(new Estado())->listar());
$smarty->assign("setores", (new Setor())->listarSetoresPai());
