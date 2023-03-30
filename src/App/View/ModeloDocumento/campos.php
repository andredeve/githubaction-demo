<?php
use App\Model\ModeloDocumento;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';

$smarty->template_dir = VIEW_PATH . 'ModeloDocumento/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign("modelo", (new ModeloDocumento())->buscar($_POST['modelo_id']));
$smarty->assign("file_version", uniqid());
$smarty->display('campos.tpl');