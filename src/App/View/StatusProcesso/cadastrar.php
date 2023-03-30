<?php

use App\Model\StatusProcesso;

$smarty->assign('page_title', 'Novo Status');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'cadastre um novo status de processo');
$smarty->assign("acao", "inserir");
$smarty->assign("status", new StatusProcesso());


