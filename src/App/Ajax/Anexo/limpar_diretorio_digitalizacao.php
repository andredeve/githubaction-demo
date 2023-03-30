<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 20/02/2019
 * Time: 11:17
 */
include "../../../../bootstrap.php";
$base = DIGITALIZACAO_PATH;
$usuario_logado = \App\Controller\UsuarioController::getUsuarioLogadoDoctrine();
$dir = $base . $usuario_logado->getNomePastaDigitalizacao() . "/";
if (is_dir($dir)) {
    $diretorio = dir($dir);
    while ($arquivo = $diretorio->read()) {
        if ($arquivo != '.' && $arquivo != '..') {
            unlink($dir . $arquivo);
        }
    }
    $diretorio->close();
}

