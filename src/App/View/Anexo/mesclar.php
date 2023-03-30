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
$processo = isset($_POST['processo_id']) ? (new \App\Model\Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
$processo->getComponentes(); // Workaround: Por motivo desconhecido, pode ocorrer de o anexo não possuir Compenente, fazendo com que quebre o algoritmo. Adicionado essa chamada para garantir que o Componente tenha sido criado.
$smarty->assign("processo", $processo);
include VIEW_PATH . 'Anexo/_assign.php';
$smarty->display('mesclar.tpl');
