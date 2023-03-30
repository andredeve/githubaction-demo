<?php

require '../../../../bootstrap.php';

$_SESSION["execucao_script"] = 1;

$usuario = new \App\Model\Usuario();
$usuario->setAtivo(1);
$usuario->setCargo("Cadastro Processo Externo");
$usuario->setDataCadastro(new DateTime());
$usuario->setEmail("usuario-interessado@lxtec.com.br");
$usuario->setTipo(App\Enum\TipoUsuario::INTERESSADO);
$usuario->setLogin("usuario-interessado");
$usuario->setNome('Usuário Interessado');
$usuario->setSenha(\App\Model\Usuario::codificaSenha("lx121314"));
$usuario->inserir();

try{
    $ini_path = APP_PATH."_config/parametros.ini";
    $fp = fopen($ini_path, "a+");
    $res_user = fwrite($fp, "\nusuario_interessado_id=".$usuario->getId());
    fclose($fp);
    echo "<br/>";
    echo "############################################";
    echo "<br/>";
    echo "<h5>Sucesso</h5>";
    echo "Usuário gerado: id={$usuario->getId()}";
    echo "Verifique o parametros.ini -> <b>usuario_interessado_id={$usuario->getId()}</b>";
    echo "<br/>";
    echo "############################################";
    echo "<br/>";
} catch(Exception $e){
    echo "############################################";
    echo "<br/>";
    echo "<h5>Alerta</h5>"
        ."Parâmetro->"
        ." <b>usuario_interessado_id={$usuario->getId()}</b>"
        ." não foi atualizado em _config/parametros.ini. "
        ."<b>Adicione manualmente.</b>";
    echo "<br/>";
    echo "############################################";
}