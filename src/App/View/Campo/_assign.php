<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Model\TipoAnexo;

$smarty->assign("tipos_documento", (new TipoAnexo())->listar());
$smarty->assign("nomenclatura", \App\Controller\IndexController::getParametosConfig()['nomenclatura']);