<?php
/**********************************/
/***Última Alteração: 03/02/2023***/
/*************André****************/
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$anexo = unserialize($_SESSION['anexo']);
if (!empty($_POST['anexo_id'])) {
    $anexo_atualizar = (new \App\Model\Anexo())->buscar($_POST['anexo_id']);
    $anexo->setTipo($anexo_atualizar->getTipo());
}
$processo = !empty($_POST['processo_id']) ? (new \App\Model\Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
$anexo->setProcesso($processo);
if (isset($_SESSION['anexo']) && !empty($_POST['anexo_id'])) {
    $imagens = unserialize($_SESSION['anexo'])->getImagens();
    foreach ($imagens as $imagem) {
        $anexo->adicionaImagem($imagem);
    }
}
$smarty->assign('anexo', $anexo);
$smarty->display('fileinput.tpl');