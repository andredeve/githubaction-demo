<?php

use Core\Util\Functions;

$smarty->assign("page_title", 'Central de Notificações');
$smarty->assign('page_icon', 'fa fa-envelope-o');
$smarty->assign('hoje', Functions::dataAtual());
$smarty->assign('selected', $_REQUEST['selected']);
$smarty->assign('data_atual', new DateTime());
include VIEW_PATH . 'Notificacao/_assign.php';


