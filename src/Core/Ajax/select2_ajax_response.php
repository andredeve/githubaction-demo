<?php

include '../../../bootstrap.php';
$entidade = "App\Model\\" . $_GET['entidade'];
// TODO criar listagem para somente assunto externo
$json = array();
$busca = isset($_GET['search']) ? $_GET['search'] : null;
$pagina = isset($_GET['page']) ? $_GET['page'] : 1;
$selecionado_id = isset($_GET['id']) ? $_GET['id'] : null;
$entidade_object = new $entidade();
if (empty($selecionado_id)) {
    foreach ($entidade_object->listarSelect2($busca, $pagina) as $obj) {
        $json[] = array('id' => $obj['id'], 'text' => $obj['text']);
    }
} else {
    $entidade_object = $entidade_object->buscar($selecionado_id);
    $nome = (in_array(mb_strtolower($_GET['entidade']),['interessado','usuario']) ? $entidade_object->getPessoa()->getNome() : $entidade_object->getNome());
    $json = array('id' => $entidade_object->getId(), 'text' => $nome, "selected" => true);
}
echo json_encode(array("results" => $json, "pagination" => array("more" => true)));
