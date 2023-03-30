<?php

use App\Model\TipoAnexo;

include '../../../../bootstrap.php';


$tipoAnexo = new TipoAnexo();
$tipoAnexo = $tipoAnexo->buscar($_POST["tipo"]);
if (!is_null($tipoAnexo)) {
    echo json_encode(array("altera_vencimento"=> $tipoAnexo->getAlteraVencimento()));
} else {
    echo "[]";
}