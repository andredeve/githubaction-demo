<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 20/12/2018
 * Time: 15:28
 */
$smarty->assign('page_title', 'Nova Localização Física  de Processo');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'Endereçamento de Documento');
$smarty->assign("acao", "inserir");
$smarty->assign("localizacao", new \App\Model\LocalizacaoFisica());
include VIEW_PATH . 'LocalizacaoFisica/_assign.php';