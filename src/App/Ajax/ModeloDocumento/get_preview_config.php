<?php
/**********************************/
/***Última Alteração: 03/02/2023***/
/*************André****************/

use App\Model\Anexo;
use App\Model\ModeloDocumento;
use App\Controller\ModeloDocumentoController;

include_once "../../../../bootstrap.php";

if (isset($_POST['modelo_id'])){
    $controller = new ModeloDocumentoController();
    $modelo = $controller->buscar($_POST['modelo_id']);
    $file = $modelo->getArquivo(true);
    $preview_config = array(
        "caption" => $modelo->getArquivo(),
        "size" => is_file($file) ? filesize($file) : null,
        "type" => "office",
        "url" => false,
        "downloadUrl" => ModeloDocumento::getPathUrl() . $modelo->getArquivo(),
        "key" => null,
        "texto" => $modelo->getTexto()
    );
    header('Content-type: application/json');
    echo json_encode(array("preview_config" => $preview_config));
}
else
{
    // Quando for editar um modelo com anexo
    $anexo_id = $_POST['anexo_id'];
    $controller = new Anexo();
    $anexo = $controller->buscar($anexo_id);
    $preview_config = array(
        "texto_ocr" => $anexo->getTextoOCR()
    );
    header('Content-type: application/json');
    echo json_encode(array("preview_config" => $preview_config));
}


