<?php

use Core\Controller\AppController;

require_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = VIEW_PATH . 'Public/Templates/';
//$classeController = 'App\Controller\\' . AppController::getControllerName($_POST['entidade']);

$anexo = (new App\Model\Anexo())->buscar($_POST['objeto_id']);



//ob_start();
//echo __FILE__ . ' LINHA: ' . __LINE__;
//echo '<pre>';
//var_dump($anexo->getId());
//echo '</pre>';
//$print_log = ob_get_contents();
//ob_clean();
//echo $print_log;

$processo = $anexo->getProcesso();

$options = array();
foreach($processo->getAnexos() as $anexo){
    $options[$anexo->getId()] = $anexo;
}


$smarty->assign("options", $options);
if (isset($_POST['valores_selecionados'])) {
    $selecionados = is_array($_POST['valores_selecionados']) ? $_POST['valores_selecionados'] : array();
} else {
    $selecionados = array();
}
$selecionados[] = $_POST['objeto_id'];
$smarty->assign("option_selected", $selecionados);
$smarty->display('caixa_selecao.tpl');
