<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 11/01/2019
 * Time: 09:54
 */
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Remessa/Templates/';
$smarty->assign('app_url', APP_URL);
$dataIni = \Core\Util\Functions::converteDataParaMysql($_POST['periodoIni']);
$dataFim = \Core\Util\Functions::converteDataParaMysql($_POST['periodoFim']);
$smarty->assign('resultado', (new \App\Model\Tramite())->buscarRemessa($dataIni, $dataFim, $_POST['setor_origem_id'], $_POST['responsavel_origem_id'], $_POST['setor_destino_id'], $_POST['responsavel_destino_id']));
$smarty->display("gerar.tpl");