<?php

use App\Model\Pergunta;
use App\Model\SetorFase;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Pergunta/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'cadastre uma nova pergunta');
$smarty->assign("acao", "inserir");
$setorFase = (new SetorFase())->buscar($_POST['objeto_ref_id']);
$pergunta = new Pergunta();
$pergunta->setIsAtiva(true);
$pergunta->setOrdem(count($setorFase->getPerguntas()) + 1);
$pergunta->setSetorFase($setorFase);
$smarty->assign("pergunta", $pergunta);
include VIEW_PATH . 'Pergunta/_assign.php';
$smarty->display('formulario.tpl');
