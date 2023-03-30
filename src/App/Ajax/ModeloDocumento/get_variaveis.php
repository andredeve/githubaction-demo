<?php
use App\Model\ModeloDocumento;

include_once "../../../../bootstrap.php";

$modelo = ModeloDocumento::buscar($_POST['modelo_id']);
echo json_encode(array('variaveis' => $modelo->getVariaveis()));