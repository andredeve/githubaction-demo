<?php

use App\Controller\PerguntaController;

include '../../../../bootstrap.php';
echo (new PerguntaController())->ordenar();
