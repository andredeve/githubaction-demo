<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 08/01/2019
 * Time: 08:43
 */
$smarty->assign('page_title', 'Criar Relatório de Remessa');
$smarty->assign('page_icon', 'fa fa-plus');
$smarty->assign('page_description', 'busque por processos disponíveis que não fazem parte de nenhuma remessa');
$smarty->assign("setores", (new \App\Model\Setor())->listarSetoresPai());
$smarty->assign("responsaveis", (new \App\Model\Usuario())->listarUsuarios());
$smarty->assign('setor_selecionado', null);
$smarty->assign("hoje", Date('d/m/Y'));
$smarty->assign("acao", "inserir");
