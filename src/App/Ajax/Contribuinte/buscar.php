<?php
use App\Model\Processo;

include '../../../../bootstrap.php';
$processo = (new Processo())->buscar($_POST['processo_id']);
$response = new stdClass();
$response->numero = $processo->getNumero();
$response->exercicio = $processo->getExercicio();
$response->data = $processo->getDataAbertura(true);
$response->interessado = $processo->getInteressado()->getPessoa()->getNome();
$response->assunto = $processo->getAssunto()->getDescricao();
$response->objeto = $processo->getObjeto();
echo json_encode((array)$response);