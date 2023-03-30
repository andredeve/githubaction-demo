<?php

use App\Model\Anexo;
use App\Model\TipoAnexo;
use App\Model\Usuario;

$smarty->assign('page_title', 'Anexos por período');
$smarty->assign('page_icon', 'fa fa-paperclip');
$smarty->assign('page_description', 'Confira todos os arquivos anexados à processos no sistema');

$tipo_documento_id = null;
if(!isset($_POST['data_periodo_ini']) || empty($_POST['data_periodo_ini'])){
    $dataIni = new DateTime();
    $dataIni->sub(new DateInterval('P30D'));
    $_POST['data_periodo_ini'] = $data_ini = $dataIni->format("d/m/Y");
    $dataFim = new DateTime();
    $_POST['data_periodo_fim'] = $data_fim  = $dataFim->format("d/m/Y");
}
        

$smarty->assign("data_ini", $data_ini);
$smarty->assign("data_fim", $data_fim);


$smarty->assign("anexos", (new Anexo())->listarAnexos($tipo_documento_id, $data_ini, $data_fim));
$anexos = (new Anexo())->listarQtde("tipo" , $tipo_documento_id, $data_ini, $data_fim);

$smarty->assign("anexos_por_tipo", $anexos);
$smarty->assign("tipos_documento", (new TipoAnexo())->listar());
$smarty->assign("usuarios", (new Usuario())->listarUsuarios());
