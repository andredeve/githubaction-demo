<?php

use App\Model\Processo;
use App\Model\Setor;
use App\Model\StatusProcesso;
use App\Model\Usuario;

$smarty->assign('page_title', 'Pesquisa Avançada de ' . \Core\Controller\AppController::getParametosConfig('nomenclatura') . 's');
$smarty->assign('page_icon', 'fa fa-search');
$smarty->assign('status_processo', (new StatusProcesso())->listar());
$smarty->assign("setores", (new Setor())->listar());
$smarty->assign("usuarios", (new Usuario())->listarUsuarios());
$smarty->assign("exercicios", (new Processo())->getExercicios());
$smarty->assign("tipos_anexo", (new \App\Model\TipoAnexo())->listar());

