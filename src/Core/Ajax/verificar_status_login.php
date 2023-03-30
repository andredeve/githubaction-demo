<?php

use App\Controller\UsuarioController;

include '../../../bootstrap.php';


$urlCadContribuinte = APP_URL."contribuinte/signup";
$urlExcept = [
    "cep.republicavirtual.com.br",
    APP_URL."interessado/inserir",
];

if (strpos($_POST['url_request'], $urlExcept[0]) !== false) {
    $_POST['url_request'] = explode("/",$_POST['url_request'])[2];
}


if(isset($_POST['location']) && isset($_POST['url_request']) && $_POST['location'] == $urlCadContribuinte && in_array($_POST['url_request'], $urlExcept)){
    echo 1;
}else{
    echo UsuarioController::isLogado() ? 1 : 0;
}
