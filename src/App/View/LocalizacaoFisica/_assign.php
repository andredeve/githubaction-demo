<?php

use App\Model\Classificacao;
use App\Modeo\Usuario;

$smarty->assign("locais", (new \App\Model\Local())->listar());
$smarty->assign("tipos_local", (new \App\Model\TipoLocal())->listar());
$smarty->assign("subtipos_local", (new \App\Model\SubTipoLocal())->listar());