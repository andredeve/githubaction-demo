<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 10/12/2018
 * Time: 10:30
 */
$smarty->assign('page_title', 'Trâmites de Processos');
$smarty->assign('page_icon', 'fa fa-send-o');
$smarty->assign('groupColumn', "");
$smarty->assign('hoje', Date('d/m/Y'));
$smarty->assign('setor_selecionado', null);
include VIEW_PATH . 'Relatorio/_assign.php';