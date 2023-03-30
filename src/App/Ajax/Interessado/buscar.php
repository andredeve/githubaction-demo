<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 21/01/2019
 * Time: 16:55
 */
include '../../../../bootstrap.php';
$interessado = (new \App\Model\Interessado())->buscar($_POST['interessado_id']);
$response = new stdClass;
$response->id = $interessado->getId();
$response->nome = $interessado->getPessoa()->getNome();
echo json_encode((array)$response);