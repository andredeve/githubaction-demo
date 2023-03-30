<?php

use App\Model\Processo;
use App\Model\Tramite;
use App\Model\Usuario;

include_once '../../../../bootstrap.php';
if (isset($_POST['processo_id'])) {
    $tramite = (new Processo())->buscar($_POST['processo_id'])->getTramiteAtual();
} elseif (isset($_POST['tramite_id'])) {
    $tramite = (new Tramite())->buscar($_POST['tramite_id']);
} else {
    $tramite = null;
}
$responsavel = $tramite != null ? $tramite->getResponsavel() : new Usuario();
echo json_encode(array(
    'id' => $responsavel->getId(),
    'nome' => $responsavel->getPessoa()->getNome()
));
