<?php

use App\Model\Interessado;

include '../../../../bootstrap.php';

$interessados = array();
$nome = filter_input(INPUT_GET, 'term');
$resultado = (new Interessado())->buscarPorNome($nome);
if ($resultado != null) {
    foreach ($resultado as $interessado) {
        $interessados[] = $interessado->getPessoa()->getNome();
    }
}
echo json_encode($interessados);
