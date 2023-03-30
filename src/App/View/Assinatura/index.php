<?php

//$smarty->assign("page_title", 'Listagem de Assuntos cadastrados');
//$smarty->assign('page_icon', 'fa fa-th-list');
$smarty->assign('url_assinatura', $_REQUEST["url_lxsign"]);

$smarty->assign("usuario", null);
if(isset($_REQUEST["usuario"])){
    $smarty->assign("usuario", $_REQUEST["usuario"]);
}