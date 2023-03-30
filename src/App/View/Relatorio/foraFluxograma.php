<?php

use App\Model\Processo;

$nomenclatura = \Core\Controller\AppController::getParametosConfig()['nomenclatura'];
$smarty->assign('page_title', $nomenclatura . 's corrigidos');
$smarty->assign('nomenclatura', $nomenclatura);
$smarty->assign('page_icon', 'fa fa-files-o');
$smarty->assign('groupColumn', "");

$dataInicio = isset($_POST['periodo_ini']) ? $_POST['periodo_ini'] : '01/'.Date('m/Y');
$dataFim = isset($_POST['periodo_fim']) ? $_POST['periodo_fim'] : Date('d/m/Y');
$assunto_id = isset($_POST['assunto_id'])? $_POST['assunto_id']:null;
$interessado_id = isset($_POST['interessado_id'])?$_POST['interessado_id']:null;

$smarty->assign('assunto_id', $assunto_id);
$smarty->assign('interessado_id', $interessado_id);
$smarty->assign('interessado', !empty($interessado_id)? (new App\Model\Interessado())->buscar($interessado_id):null);
$smarty->assign('dataInicio', $dataInicio);
$smarty->assign('dataFim', $dataFim);
$smarty->assign('assuntos', (new App\Model\Assunto())->listar());
$processos =(new Processo())->listarQtdeDeTramitesForaDoFluxo($dataInicio, $dataFim, $assunto_id, $interessado_id);
$smarty->assign('processos', $processos);

$processosPorInteressado = array();
foreach($processos as $p){
    $i = $p[0]->getInteressado();
    
    if(!isset($processosPorInteressado[$i->getId()])){
        $processosPorInteressado[$i->getId()] = array(
            "interessado" => $i, "qtde_total" => 0, 
            "qtde_devolvido_0" => 0, "qtde_devolvido_1" => 0, 
            "qtde_devolvido_2" => 0, "qtde_devolvido_3" => 0,  
            "qtde_devolvido_mais" => 0, "qtde_total_devolvido" => 0);
    }
    
    $processosPorInteressado[$i->getId()]["qtde_total"]++;
    if($p['qtde_fora_fluxo'] == 0){
        $processosPorInteressado[$i->getId()]["qtde_devolvido_0"]++;
//        $processosPorInteressado[$i->getId()]["qtde_total_devolvido"]++;
    }else if($p['qtde_fora_fluxo'] == 1){
        $processosPorInteressado[$i->getId()]["qtde_devolvido_1"]++;
        $processosPorInteressado[$i->getId()]["qtde_total_devolvido"]++;
    }else if($p['qtde_fora_fluxo'] == 2){
        $processosPorInteressado[$i->getId()]["qtde_devolvido_2"]++;
        $processosPorInteressado[$i->getId()]["qtde_total_devolvido"]++;
    }else if($p['qtde_fora_fluxo'] == 3){
        $processosPorInteressado[$i->getId()]["qtde_devolvido_3"]++;
        $processosPorInteressado[$i->getId()]["qtde_total_devolvido"]++;
    }else if($p['qtde_fora_fluxo'] > 3){
        $processosPorInteressado[$i->getId()]["qtde_devolvido_mais"]++;
        $processosPorInteressado[$i->getId()]["qtde_total_devolvido"]++;
    }
}

$smarty->assign('processosPorInteressado', $processosPorInteressado);        
include VIEW_PATH . 'Relatorio/_assign.php';
