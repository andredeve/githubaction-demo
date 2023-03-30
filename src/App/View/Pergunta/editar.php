<?php

use App\Model\Pergunta;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Pergunta/Templates/';
$smarty->assign('app_url', APP_URL);
$smarty->assign('page_description', 'edita uma pergunta');
$smarty->assign("acao", "atualizar");
$smarty->assign("pergunta", (new Pergunta())->buscar($_POST['entidade_id']));
include VIEW_PATH . 'Pergunta/_assign.php';
$smarty->display('formulario.tpl');

