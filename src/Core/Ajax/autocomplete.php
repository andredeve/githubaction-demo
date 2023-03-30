<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 08/01/2019
 * Time: 09:21
 */
require_once '../../../bootstrap.php';
$entidade = "App\Model\\" . $_GET['entidade'];
$json = array();
$busca = isset($_GET['search']) ? $_GET['search'] : null;
$entidade_object = new $entidade();
foreach ($entidade_object->listarSelect2($busca) as $obj) {
    $json[] = array('id' => $obj['id'], 'label' => $obj['text']);
}
echo json_encode($json);
exit;