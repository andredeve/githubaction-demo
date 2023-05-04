<?php

use App\Model\Assunto;
use App\Model\Setor;

$smarty->assign("assuntos", (new Assunto())->listarAtivos());
$smarty->assign("setores", (new Setor())->listarSetoresPai());