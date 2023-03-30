<?php

require '../../../../bootstrap.php';

try{
    $_SESSION["execucao_script"] = 1;
    $usuario = new \App\Model\Usuario();
    $senha = "lx121314";
    $usuario->setAtivo(1);
    $usuario->setCargo("Suporte Lxtec");
    $usuario->setDataCadastro(new DateTime());
    $usuario->setEmail("victor@lxtec.com.br");
    $usuario->setTipo(App\Enum\TipoUsuario::MASTER);
    $usuario->setLogin("admin");
    $usuario->setNome('Lxtec');
    $usuario->setSenha(\App\Model\Usuario::codificaSenha($senha));
    $usuario->inserir();

    echo "<br/>";
    echo "############################################";
    echo "<br/>";
    echo "<h5>Sucesso</h5>";
    echo "Login: {$usuario->getLogin()}";
    echo "<br/>";
    echo "Senha: {$senha}";
    echo "<br/>";
    echo "############################################";
    echo "<br/>";
}catch (Exception $e){
    echo "############################################";
    echo "<br/>";
    echo "<h4>Alerta</h4>"
        ."Não foi possível inserir usuário";
    echo "<br/>";
    echo "<p><b>Error:</b> {$e->getMessage()}</p>";
    echo "<br/>";
    echo "############################################";
}