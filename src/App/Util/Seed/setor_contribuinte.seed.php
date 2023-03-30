<?php

require '../../../../bootstrap.php';

$_SESSION["execucao_script"] = 1;

try{
    $setor = new \App\Model\Setor();
    $setor->setIsExterno(1);
    $setor->setArquivar(0);
    $setor->setDataCadastro(new DateTime());
    $setor->setNome("CONTRIBUINTE");
    $setor->setSigla("CONTRIB");
    $setor->setIsAtivo(1);
    $setor->setDisponivelTramite(1);
    $setor->inserir();
    $ini_path = APP_PATH."_config/parametros.ini";
    $fp = fopen($ini_path, "a+");
    $res_setor = fwrite($fp, "\nprocesso_setor_contribuinte_id=".$setor->getId());
    fclose($fp);
    echo "<br/>";
    echo "############################################";
    echo "<br/>";
    echo "<h5>Sucesso</h5>";
    echo "Setor gerado: id={$setor->getId()}";
    echo "Verifique o parametros.ini -> <b>processo_setor_contribuinte_id={$setor->getId()}</b>";
    echo "<br/>";
    echo "############################################";
    echo "<br/>";

}catch (Exception $e){
    echo "############################################";
    echo "<br/>";
    echo "<h5>Alerta</h5>"
        ."Parâmetro->"
        ." <b>processo_setor_contribuinte_id={$setor->getId()}</b>"
        ." não foi atualizado em _config/parametros.ini. "
        ."<b>Adicione manualmente.</b>";
    echo "<br/>";
    echo "############################################";
}