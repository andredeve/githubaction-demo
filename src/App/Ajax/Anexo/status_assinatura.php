<?php

use Core\Controller\AppController;

require_once '../../../../bootstrap.php';

$_POST['anexo_id'] ;
$anexo = new App\Model\Anexo();
$anexo = $anexo->buscar($_POST['anexo_id']);
$assinatura = App\Model\Assinatura::buscarPorAnexo($anexo);
//$assinatura =  $anexo->getAssinatura()->get(0);
echo json_encode(App\Controller\ProcessoController::buscarStatusAssinaturas(array($assinatura->getLxsign_id())));