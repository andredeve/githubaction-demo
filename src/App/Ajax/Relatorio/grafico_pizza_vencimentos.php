<?php

use App\Model\Processo;

include '../../../../bootstrap.php';
$referencia = $_POST['referencia'];
$processo = new Processo();
$setorAtual = isset($_POST['setor_atual_id']) && !empty($_POST['setor_atual_id']) ? (new \App\Model\Setor())->buscar($_POST['setor_atual_id']) : null;
$assunto = isset($_POST['assunto_id']) && !empty($_POST['assunto_id']) ? (new \App\Model\Assunto())->buscar($_POST['assunto_id']) : null;
$interessado = isset($_POST['interessado_id']) && !empty($_POST['interessado_id']) ? (new \App\Model\Interessado())->buscar($_POST['interessado_id']) : null;
$responsavel = isset($_POST['responsavel_id']) && !empty($_POST['responsavel_id']) ? (new \App\Model\Usuario())->buscar($_POST['responsavel_id']) : null;
$vencimentoIni = isset($_POST['data_vencimento_ini']) && !empty($_POST['data_vencimento_ini']) ? \Core\Util\Functions::converteDataParaMysql($_POST['data_vencimento_ini']) : null;
$vencimentoFim = isset($_POST['data_vencimento_fim']) && !empty($_POST['data_vencimento_fim']) ? \Core\Util\Functions::converteDataParaMysql($_POST['data_vencimento_fim']) : null;
$result = $processo->listarQtdeTramitesVencidos($referencia, $setorAtual, $assunto, $interessado, $responsavel, $vencimentoIni, $vencimentoFim);
$response = array();
foreach ($result as $r) {
    $tramite = $r[0];
    switch ($referencia) {
        case 'interessado':
        case 'assunto':
            $processo = $tramite->getProcesso();
            $getMethod = 'get' . ucfirst($referencia);
            $classe = get_class($processo->$getMethod());
            $nome = method_exists($classe, 'getNome') ? $processo->$getMethod()->getNome() : $processo->$getMethod()->getDescricao();
            break;
        case 'setorAtual':
            $getMethod = 'get' . ucfirst($referencia);
            $classe = get_class($tramite->$getMethod());
            $nome = method_exists($classe, 'getNome') ? $tramite->$getMethod()->getNome() : $tramite->$getMethod()->getDescricao();
            break;
    }
    $qtde = (int)$r['qtde'];
    $response[] = array(
        'name' => empty($nome) ? "Não informado" : $nome,
        'y' => $qtde,
        'sliced' => false,
        'selected' => false
    );
}
echo json_encode($response);

