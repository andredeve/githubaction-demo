<?php

use App\Model\StatusProcesso;

include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$smarty->assign('app_url', APP_URL);
$processo = (new \App\Model\Processo())->buscar($_POST['processo_id']);
$assunto = isset($_POST['assunto_id']) && !empty($_POST['assunto_id']) ? (new \App\Model\Assunto())->buscar($_POST['assunto_id']) : $processo->getAssunto();
$numero_fase = $assunto != $processo->getAssunto() ? 1 : $processo->getNumeroFase();
$smarty->assign("setores_fase", $assunto->getFluxograma()->getFases($numero_fase)->getSetoresFase());
$smarty->assign('processo', $processo);
$smarty->assign('numero_fase', $numero_fase);
$smarty->assign('assunto', $assunto);
$smarty->assign('tramite', $processo->getTramiteAtual());
$smarty->assign('setor_origem', $processo->getTramiteAtual()->getSetorAtual());
$smarty->assign("status_processo", (new StatusProcesso())->listarAtivos());
$smarty->assign("status_inicial_id", 2);
if ($numero_fase != 1) {
    $smarty->assign("setores", (new \App\Model\Setor())->listarSetoresPai());
}
$smarty->display('destino.tpl');