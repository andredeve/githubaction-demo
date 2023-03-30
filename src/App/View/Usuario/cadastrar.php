<?php

use App\Model\Pessoa;
use App\Model\Usuario;

$smarty->assign('page_title', 'Novo Usuário');
$smarty->assign('page_icon', 'fa fa-user-plus');
$smarty->assign('page_description', 'cadastre um novo usuário para ter acesso ao sistema');
$smarty->assign("acao", "inserir");
$usuario = new Usuario();
$usuario->setPessoa(new Pessoa());
$usuario->setAtivo(true);
$smarty->assign("usuario", $usuario);
$smarty->assign('setor_selecionado', null);
include VIEW_PATH . 'Usuario/_assign.php';

