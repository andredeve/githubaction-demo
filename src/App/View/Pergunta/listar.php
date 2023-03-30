<?php

use App\Model\SetorFase;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Pergunta/Templates/';
$smarty->assign('app_url', APP_URL);
$setorFase = (new SetorFase())->buscar($_POST['objeto_ref_id']);
$smarty->assign("setor_fase", $setorFase);
$smarty->assign("perguntas", $setorFase->getPerguntas());
$smarty->display('tabela.tpl');
