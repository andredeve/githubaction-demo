<?php

use App\Model\Processo;

$smarty->assign('page_title', 'Processos Arquivados');
$smarty->assign('page_icon', 'fa fa-archive');
$smarty->assign('groupColumn', "");
$smarty->assign('processos', (new Processo())->listarArquivados(false));
include VIEW_PATH . 'Relatorio/_assign.php';
