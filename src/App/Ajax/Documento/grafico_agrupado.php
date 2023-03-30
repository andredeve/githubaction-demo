<?php

use App\Model\Documento;

include '../../../../bootstrap.php';
$agrupado = $_POST['agrupado'];
$documento = new Documento();
$result = $documento->listarQtdeAgrupada($agrupado);
$response = array();
foreach ($result as $r) {
    $documento = $r[0];
    $getMethod = 'get' . ucfirst($agrupado);
    $classe = get_class($documento->$getMethod());
    $nome = method_exists($classe, 'getNome') ? $documento->$getMethod()->getNome() : $documento->$getMethod()->getDescricao();
    $qtde = (int) $r['qtde'];
    adicionarResultado($nome, $qtde);
}
echo json_encode($response);

function adicionarResultado($nome, $qtde) {
    global $response;
    $achou = false;
    foreach ($response as $r) {
        if ($r['name'] == $nome) {
            $achou = true;
            $r['y'] += $qtde;
        }
    }
    if (!$achou) {
        $response[] = array(
            'name' => $nome,
            'y' => $qtde,
            'sliced' => false,
            'selected' => false
        );
    }
}
