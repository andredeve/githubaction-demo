<?php

use Core\Controller\AppController;


$app_config = AppController::getConfig();
$smarty->assign('page_title', 'Login do Sistema');
$smarty->assign('app_url', APP_URL);
$smarty->assign('app_config', $app_config);
$smarty->assign("data_site_key", $app_config['data_site_key']);
$smarty->assign('file_version', $app_config['file_version']);