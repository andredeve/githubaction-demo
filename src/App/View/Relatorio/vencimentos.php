<?php

use App\Model\Processo;

$smarty->assign('page_title', 'Controle de Vencimentos');
$smarty->assign('page_icon', 'fa fa-clock-o');
$smarty->assign('page_description', 'Acompanhe os processos vencidos e notifique os responsáveis se necessário');
$smarty->assign("tramites_vencidos", (new Processo())->listarTramitesVencidos());
include VIEW_PATH . 'Relatorio/_assign.php';
