<?php

error_reporting(E_ERROR);

use App\Model\Anexo;

include '../../../../bootstrap.php';
$group_by = $_POST['referencia'];
$tipo_documento_id = $_POST['tipo_documento_id'];
$data_ini = $_POST['data_periodo_ini'];
$data_fim = $_POST['data_periodo_fim'];
$usuario_id = $_POST['usuario_id'];
$result = (new Anexo())->listarQtde($group_by, $tipo_documento_id, $data_ini, $data_fim, $usuario_id);
$response = array();
if ($group_by == 'tipo') {
    foreach ($result as $r) {
        $anexo = $r[0];
        $nome = $anexo->getTipo() != null ? $anexo->getTipo()->getDescricao() : "Não Informado";
        $qtde = (int) $r['qtde'];
        $response[] = array(
            'name' => $nome,
            'y' => $qtde,
            'sliced' => false,
            'selected' => false
        );
    }
} else if ($group_by == 'isDigitalizado') {
    foreach ($result as $r) {
        $anexo = $r[0];
        $nome = $anexo->getIsDigitalizado() ? "Digitalizados" : "Não Digitalizados";
        $qtde = (int) $r['qtde'];
        $response[] = array(
            'name' => $nome,
            'y' => $qtde,
            'sliced' => false,
            'selected' => false
        );
    }
}
echo json_encode($response);

