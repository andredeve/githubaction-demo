<?php

use App\Model\Assunto;
use App\Model\Interessado;
use App\Model\Processo;
use App\Model\Setor;
use App\Model\Usuario;

$smarty->assign("exercicios", (new Processo())->getExercicios());
$smarty->assign("setores", (new Setor())->listar());
$smarty->assign("usuarios", (new Usuario())->listarUsuarios());
