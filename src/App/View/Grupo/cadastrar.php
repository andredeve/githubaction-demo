<?php

use App\Model\Grupo;
use App\Model\PermissaoEntidade;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

$smarty->assign('page_title', 'Novo Grupo');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre um novo grupo');
$smarty->assign("acao", "inserir");
$grupo = new Grupo();
$permissoes_entidade = new ArrayCollection();
foreach (PermissaoEntidade::getEntidades() as $entidade) {
    $permissao = new PermissaoEntidade();
    $permissao->setCodigoEntidade($entidade['codigo']);
    $permissao->setGrupo($grupo);
    $permissoes_entidade->add($permissao);
}
$grupo->setPermissoesEntidade($permissoes_entidade);
$smarty->assign("grupo", $grupo);
include VIEW_PATH . 'Grupo/_assign.php';
