<?php

use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Classificacao;
use App\Model\TipoAnexo;

$usuario_logado = \App\Controller\UsuarioController::getUsuarioLogadoDoctrine();
$permitir_digitalizacao = $usuario_logado->getNomePastaDigitalizacao() != null && is_dir(DIGITALIZACAO_PATH . $usuario_logado->getNomePastaDigitalizacao()) ? 1 : 0;
$smarty->assign("permitir_digitalizacao", $permitir_digitalizacao);
$smarty->assign("tipos_documento", (new TipoAnexo())->listarAtivos());
$smarty->assign("classificacoes", (new Classificacao())->listar());
$usuarioEhInteressado = UsuarioController::isInteressado();
$smarty->assign("usuarioEhInteressado", $usuarioEhInteressado);
$smarty->assign("indice", $_POST['indice'] ?? count($processo->getAnexos()));
$smarty->assign("parametros", App\Controller\IndexController::getParametosConfig());
if(isset($assinatura)){
    $smarty->assign("assinatura", $assinatura);
}else{
    $smarty->assign("assinatura", null);
}