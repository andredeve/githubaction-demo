<?php

use Core\Util\Email;

require_once '../../../bootstrap.php';
$email = new Email();
echo $email->enviarSolicitacaoSuporte();
