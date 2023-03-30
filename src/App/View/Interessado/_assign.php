<?php

use App\Controller\UsuarioController;
use App\Model\Estado;
use App\Model\Setor;

$smarty->assign("entidade", "interessado");
$smarty->assign("estados",(new Estado())->listar());
$smarty->assign("setores",(new Setor())->listarAtivos());
if($processo_externo){
    $usuarioEhInteressado = $processo_externo;
}else{
    $usuarioEhInteressado = UsuarioController::isInteressado();
}
$smarty->assign('usuarioEhInteressado', $usuarioEhInteressado);