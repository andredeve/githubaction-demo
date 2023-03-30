<?php

use App\Controller\UsuarioController;

$smarty->assign('page_title', 'Alterar senha');
$smarty->assign('page_description', 'altere sua senha de acesso ao sistema');
$smarty->assign('page_icon', 'fa fa-lock');
$smarty->assign("usuario", UsuarioController::getUsuarioLogado());
