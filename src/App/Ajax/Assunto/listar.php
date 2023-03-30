<?php

use App\Model\Assunto;

include '../../../../bootstrap.php';

$assuntos = array();
$descricao = filter_input(INPUT_GET, 'term');
foreach ((new Assunto())->listarPorDescricao($descricao) as $assunto) {
    $assuntos[] = $assunto->getDescricao();
}
echo json_encode($assuntos);
