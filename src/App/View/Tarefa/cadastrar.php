<?php

use App\Model\Tarefa;
use App\Model\SetorFase;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tarefa/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'cadastre uma nova tarefa');
$smarty->assign("acao", "inserir");
$setorFase = (new SetorFase())->buscar($_POST['objeto_ref_id']);
$tarefa = new Tarefa();
$tarefa->setIsAtiva(true);
$tarefa->setOrdem(count($setorFase->getTarefas()) + 1);
$tarefa->setSetorFase($setorFase);
$smarty->assign("tarefa", $tarefa);
include VIEW_PATH . 'Tarefa/_assign.php';
$smarty->display('formulario.tpl');
