<?php
use App\Model\ModeloDocumento;

$smarty->assign('page_title', 'Novo Modelo de Documento');
$smarty->assign('page_icon', 'fa fa-plus');
//$smarty->assign('page_description', 'cadastre um novo modelo de documento');
$smarty->assign("acao", "inserir");
$smarty->assign("modelo", (new ModeloDocumento()));