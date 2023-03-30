<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 21/01/2019
 * Time: 16:55
 */
include '../../../../bootstrap.php';
if (isset($_POST['anexo_id']) && !empty($_POST['anexo_id'])) {
    $anexo = (new \App\Model\Anexo())->buscar($_POST['anexo_id']);
    $response = new stdClass;
    $response->numero = $anexo->getNumero();
    $response->descricao = $anexo->getDescricao();
    $response->exercicio = $anexo->getExercicio();
    $response->data = $anexo->getData(true);
    $response->valor = $anexo->getValor();
    $response->paginas = $anexo->getQtdePaginas();
    $response->tipo = $anexo->getTipo()->getId();
    $response->classificacao = $anexo->getClassificacao() != null ? $anexo->getClassificacao()->getId() : null;
    echo json_encode((array)$response);
}
