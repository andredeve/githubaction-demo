<?php

use App\Model\Assunto;
use Core\Util\Functions;

include_once '../../../../bootstrap.php';
if (!empty($_POST['assunto_id'])) {
    $assunto_id = filter_input(INPUT_POST, 'assunto_id', FILTER_SANITIZE_NUMBER_INT);
    $data_processo = filter_input(INPUT_POST, 'data_processo');
    $assunto = (new Assunto())->buscar($assunto_id);
    echo $assunto->getVencimento(true, Functions::converteDataParaMysql($data_processo));
} else {
    echo Date('d/m/Y');
}