<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 17/01/2019
 * Time: 17:12
 */
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('parametros', http_build_query($_POST));
$smarty->display('pesquisar.tpl');
