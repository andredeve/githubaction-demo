<?php

$smarty->assign('page_title', 'Editar Grupo');
$smarty->assign('page_icon', 'fa fa-edit');
$smarty->assign("acao", "atualizar");
$grupo = $_REQUEST['objeto'];
foreach (\App\Model\PermissaoEntidade::getEntidades() as $entidade) {
    $permissao = new \App\Model\PermissaoEntidade();
    $permissao->setCodigoEntidade($entidade['codigo']);
    $permissao->setGrupo($grupo);
    $grupo->adicionaPermissaoEntidade($permissao);
}
$smarty->assign("grupo", $grupo);
include VIEW_PATH . 'Grupo/_assign.php';
