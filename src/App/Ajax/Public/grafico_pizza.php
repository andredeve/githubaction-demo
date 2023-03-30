<?php

use App\Model\Processo;

include '../../../../bootstrap.php';
$referencia = $_POST['referencia'];
$responsavel_id = $_POST['responsavel_id'];
$assunto_id = $_POST['assunto_id'];
$interessado_id = $_POST['interessado_id'];
$processo = new Processo();
$result = $processo->listarQtdeProcessos($referencia, $responsavel_id, $assunto_id, $interessado_id);
$response = array();
foreach ($result as $r) {
    $tramite = $r[0];
    $nome = $tramite->getSetorAtual()->getSigla();
    $qtde = (int) $r['qtde'];
    $response[] = array(
        'name' => empty($nome) ? mb_convert_encoding("Não informado","UTF-8") : mb_convert_encoding($nome,"UTF-8") ,
        'y' => $qtde,
        'sliced' => false,
        'selected' => false
    );
}

//ob_start();
//echo '<pre>';
//echo 'Arquivo: ' . __FILE__ . ' Linha: ' . __LINE__ . '<br>';
//echo 'Método: ' . __FUNCTION__ . '<br>';
//print_r($response);
//echo '</pre>';
//$content = ob_get_contents();
//ob_clean();
//error_log($content);
if(!empty($response)){
    echo json_encode($response);
}
