<?php

use App\Model\Setor;

include '../../../../bootstrap.php';

$setores = array();
$descricao = filter_input(INPUT_GET, 'term');
foreach ((new Setor())->buscarPorDescricao($descricao) as $setor) {
    $setores[] = $setor->getDescricao();
}
echo json_encode($setores);
