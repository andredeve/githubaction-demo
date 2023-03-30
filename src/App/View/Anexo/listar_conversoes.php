<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 06/02/2019
 * Time: 15:08
 */
include_once '../../../../bootstrap.php';
include_once '../../../../_config/smarty.config.php';
$smarty->template_dir = APP_PATH . '/src/App/View/Anexo/Templates/';
$smarty->assign('app_url', APP_URL);
$conversoes = App\Model\Converter::listarConversoesNaFila();
$smarty->assign("conversoes", $conversoes);
$smarty->display('listar_conversoes.tpl');
