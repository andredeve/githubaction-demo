<?php
use \App\Model\Converter;
use \App\Model\Anexo;

include 'bootstrap.php';

  
$email =  new \App\Util\Email();
$retorno  = $email->testEnviaEmail("victor@lxtec.com.br");


ob_start();
echo __FILE__ . ' LINHA: ' . __LINE__;
echo '<pre>';
var_dump($retorno);
echo '</pre>';
$print_log = ob_get_contents();
ob_clean();
echo $print_log;