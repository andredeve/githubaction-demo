<?php
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Tramite/Templates/';
$processo = (new \App\Model\Processo())->buscar($_POST['processo_id']);
if (!empty($_POST['assunto_id'])) {
    $assuntoProcesso = new \App\Model\AssuntoProcesso();
    $assuntoProcesso->setProcesso($processo);
    $assuntoProcesso->setAssunto($processo->getAssunto());
    $processo->adicionarAssunto($assuntoProcesso);
    $numero_fase = 1;
    $processo->setNumeroFase($numero_fase);
    $smarty->assign('assunto', (new \App\Model\Assunto())->buscar($_POST['assunto_id']));
} else {
    $numero_fase = $processo->getNumeroFase();
    $smarty->assign('assunto', $processo->getAssunto());
}
$smarty->assign('numero_fase', $numero_fase);
$smarty->assign('processo', $processo);
$smarty->assign('app_url', APP_URL);
$smarty->display('fluxograma.tpl');
