<?php

use App\Model\Setor;
use App\Model\StatusProcesso;

include '../../../../bootstrap.php';


include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$setor = new Setor();
$setor = $setor->buscar($_POST['setor_destino_id']);
$smarty->assign("status_inicial_id", !empty($_POST['status_inicial']) ? $_POST['status_inicial'] : 2);
$smarty->assign('i', $_POST['indice']);
$smarty->assign("setor_origem", !empty($_POST['setor_origem_id']) ? $setor->buscar($_POST['setor_origem_id']) : null);
$smarty->assign('setor', $setor);
$smarty->assign("status_processo", (new StatusProcesso())->listarAtivos());
$smarty->assign("qtde_linhas", $_POST['qtde_linhas']);
$smarty->assign('app_url', APP_URL);
$smarty->display('linha_envio.tpl');

