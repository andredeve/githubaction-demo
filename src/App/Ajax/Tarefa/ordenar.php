<?php

use App\Controller\TarefaController;

include '../../../../bootstrap.php';
echo (new TarefaController())->ordenar();
