<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 13/12/2018
 * Time: 16:48
 */
include '../../../../bootstrap.php';
$processo = new \App\Model\Processo();
$json = array();
$busca = isset($_GET['search']) ? $_GET['search'] : null;
$pagina = isset($_GET['page']) ? $_GET['page'] : 1;
foreach ($processo->listarSelect2($busca, $pagina, true) as $obj) {
    $json[] = array('id' => $obj['id'], 'text' => utf8_encode($obj['text']));
}
echo json_encode(array("results" => $json, "pagination" => array("more" => true)));