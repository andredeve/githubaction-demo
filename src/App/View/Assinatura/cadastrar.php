<?php

use App\Model\Assunto;

$smarty->assign('page_title', 'Cadastrar Usuário na Assinatura Digital');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastrar um novo usuário na assinatura digital');
$smarty->assign('usuario_id', $_REQUEST["usuario_id"]);