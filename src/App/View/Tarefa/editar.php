<?php

use App\Model\Tarefa;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tarefa/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'edita uma tarefa');
$smarty->assign("acao", "atualizar");
$smarty->assign("tarefa", (new Tarefa())->buscar($_POST['entidade_id']));
include VIEW_PATH . 'Tarefa/_assign.php';
$smarty->display('formulario.tpl');

