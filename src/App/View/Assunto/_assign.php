<?php

use App\Model\Assunto;

$smarty->assign("assuntos", (new Assunto())->listarAtivos());

