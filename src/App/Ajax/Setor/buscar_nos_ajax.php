<?php

use App\Model\Setor;

include '../../../../bootstrap.php';
$setores_encontrados = array();
$setor = new Setor();
$nome = filter_input(INPUT_GET, 'str', FILTER_SANITIZE_STRING);
foreach ($setor->buscarPorDescricao($nome) as $setor) {
    $setor_pai = $setor->getSetorPai();
    if ($setor_pai != null) {
        $setores_encontrados[] = $setor_pai->getId();
    }
}
echo json_encode($setores_encontrados);
