<?php

use App\Model\Fluxograma;

$smarty->assign('page_title', 'Novo Fluxograma');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre um novo fluxograma para um assunto');
$smarty->assign("acao", "inserir");
$smarty->assign('fluxograma', new Fluxograma());
include VIEW_PATH . 'Fluxograma/_assign.php';
