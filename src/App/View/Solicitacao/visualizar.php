<?php

use App\Model\Solicitacao;

include_once APP_PATH . 'bootstrap.php';
include_once APP_PATH . '_config/smarty.config.php';

$smarty->setTemplateDir(APP_PATH . 'src/App/View/Solicitacao/Templates/');
$smarty->assign('app_url', APP_URL);
$smarty->display("visualizar.tpl");