<?php
use App\Model\Usuario;
use App\Model\StatusProcesso;
use App\Model\Setor;

include './bootstrap.php';
$_SESSION["execucao_script"] = true;
try {
    (new StatusProcesso())->seed();
    (new Usuario())->seed();
    (new Setor())->seed();
    echo "\n A carga de entidades foi realizada com sucesso.";
} catch (Exception $ex) {
    trigger_error($ex->getMessage());
}

