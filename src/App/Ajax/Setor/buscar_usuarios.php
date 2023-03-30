<?php

use Core\Enum\TipoMensagem;

include '../../../../bootstrap.php';
try {
    if (isset($_POST['setor_id']) && !empty($_POST['setor_id'])) {
        $setor = (new \App\Model\Setor())->buscar($_POST['setor_id']);
        $usuarios = array();
        foreach ($setor->getUsuarios() as $usuario) {
            $d = new stdClass();
            $d->id = $usuario->getId();
            $d->nome = $usuario->getPessoa()->getNome();
            $usuarios[] = $d;
        }
    } else {
        $usuarios = null;
    }
    $tipo = TipoMensagem::SUCCESS;
    $msg = "Consulta de usuários realizada com sucesso.";
} catch (Exception $ex) {
    $tipo = TipoMensagem::ERROR;
    $msg = "Erro: " . $ex->getMessage();
    $usuarios = null;
}
echo json_encode(array(
    'tipo' => $tipo,
    'msg' => $msg,
    'usuarios' => $usuarios
));
