<?php

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Processo/Templates/';
$smarty->assign('app_url', APP_URL);
$parametros = array('vencidos' => true);
$smarty->assign('parametros_processo', http_build_query($parametros));
$smarty->display('listar.tpl');
