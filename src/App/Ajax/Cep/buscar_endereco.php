<?php
require_once "../../../../bootstrap.php";

use Core\Util\Http\Client\Builder;

$response = (new Builder("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep={$_GET['cep']}"))
    ->verifySSL(false)
    ->setParameters([
        "formato" => "javascript",
        "cep" => $_GET['cep']
    ])
    ->build()
    ->send();
echo $response->getBody()->toScalar();