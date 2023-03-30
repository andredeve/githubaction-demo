<?php

use Core\Controller\AppController;

require_once '../../../bootstrap.php';
include_once '../../../_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . 'Public/Templates/';
$classeController = 'App\Controller\\' . AppController::getControllerName($_POST['entidade']);
$smarty->assign("options", (new $classeController())->getOptions());
if (isset($_POST['valores_selecionados'])) {
    $selecionados = is_array($_POST['valores_selecionados']) ? $_POST['valores_selecionados'] : array();
} else {
    $selecionados = array();
}
$selecionados[] = $_POST['objeto_id'];
$smarty->assign("option_selected", $selecionados);
$smarty->display('caixa_selecao.tpl');
