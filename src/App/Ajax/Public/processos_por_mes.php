<?php

use App\Model\Processo;

include '../../../../bootstrap.php';

$response = array();
$qtdes = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
$ano_default = \App\Controller\ProcessoController::getExercicioAtual();
//echo $ano_default;
$ano = !empty($_POST['ano']) ? $_POST['ano'] : $ano_default;
$assunto_id = !empty($_POST['assunto_id']) ? $_POST['assunto_id'] : null;
$processo = new Processo();
$result = $processo->buscarQuantidadePorMes($ano, $assunto_id);
foreach ($result as $r) {
    $qtdes[(int)$r['mes'] - 1] = (int)$r['qtde'];
}
$response[] = array(
    'name' => 'Processo',
    'data' => $qtdes,
);
echo json_encode($response);

