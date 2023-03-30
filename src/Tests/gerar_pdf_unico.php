<?php

use App\Model\Processo;

include '../bootstrap.php';
(new Processo())->buscar(102)->gerarArquivoDigital();
