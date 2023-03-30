<?php

use App\Model\Processo;

include '../../../../bootstrap.php';

$processo = new Processo();
$agrupado = $_POST['agrupado'];
$result = $processo->listarQtdeAgrupada($agrupado, $_POST['data_ini'], $_POST['data_fim'], $_POST['qtde_registros']);
$response = array();
$getMethod = 'get' . ucwords($agrupado);
foreach ($result as $r) {
    $response[] = array(
        'name' => $r['processo']->$getMethod(true),
        'y' => (int) $r['qtde'],
        'sliced' => false,
        'selected' => false
    );
}
echo json_encode($response);


