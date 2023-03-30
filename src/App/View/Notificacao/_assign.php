<?php

use App\Model\Usuario;

//$smarty->assign("processos_disponiveis", (new Processo())->listarProcessosDisponiveis());
$smarty->assign("usuarios", (new Usuario())->listarUsuarios());
$nomenclatura = \Core\Controller\AppController::getParametosConfig()['nomenclatura'];
$smarty->assign("nomenclatura", $nomenclatura);
