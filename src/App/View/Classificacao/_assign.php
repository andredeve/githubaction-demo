<?php

use App\Model\Classificacao;
use App\Model\Usuario;

$smarty->assign("entidade", "classificacao");
$smarty->assign("classificacoes", (new Classificacao())->listar());
//$smarty->assign("usuarios", (new Usuario())->listarUsuarios());
