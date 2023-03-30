<?php
require "../../bootstrap.php";
try {
    echo "<pre>";
    (new \App\Util\Email())->enviarEmail("Teste", "Teste", array('anderson@lxtec.com.br'));
    echo "</pre>";
} catch (Exception $ex) {
    die($ex->getMessage());
}
