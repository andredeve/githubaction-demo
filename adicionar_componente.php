<?php
use \App\Model\Converter;
use \App\Model\Anexo;

include 'bootstrap.php';

$_SESSION["execucao_script"] = true;
//set_time_limit(30);
$converter = new Converter();

foreach($converter->listar() as $c){
    if($c->getDataTermino()){
        $componente = new \App\Model\Componente();
        $anexo = $c->getAnexo();
        if(!$componente->buscarPorCampos(array("anexo" =>$anexo))){
            \App\Controller\ComponenteController::inserirComponente($anexo->getProcesso(), $anexo);
        }
    }
}
//$converter = $converter->buscar(14);

