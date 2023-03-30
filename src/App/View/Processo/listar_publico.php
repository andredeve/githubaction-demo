<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 11/01/2019
 * Time: 16:02
 */

include '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Processo/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('parametros', http_build_query($_POST));
$smarty->display('listar_publico.tpl');
