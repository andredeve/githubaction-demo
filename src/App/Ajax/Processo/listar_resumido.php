<?php
/**********************************/
/***Última Alteração: 07/02/2023***/
/*************André****************/
use App\Controller\UsuarioController;
use App\Model\Dao\ProcessoDao;
use App\Controller\ProcessoController;
use App\Model\Processo;
use Core\Controller\AppController;
use Core\Util\Functions;
use Core\Util\SSP;

include '../../../../bootstrap.php';

$table = "view_processos";
// Table's primary key
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'numero', 'dt' => 1),
    array('db' => 'exercicio', 'dt' => 2),
    array('db' => 'assunto', 'dt' => 3),
    array('db' => 'interessado', 'dt' => 4),
    array('db' => 'setor_atual', 'dt' => 5,
        'formatter' => function ($d, $row) {
            if(empty($d)){
                if(!empty($row[10])){
                    $processo = (new Processo())->buscar($row[10]);
                    
                    if($processo && $processo->getSetorAtual()){
                        return $processo->getSetorAtual()->getNome();
                    }else{
                        return "";
                    }
                }else{
                    return '';
                } 
            }
            return $d;
        }
    ),
    array('db' => 'data_abertura', 'dt' => 6,
        'formatter' => function ($d, $row) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'data_vencimento_tramite', 'dt' => 7,
        'formatter' => function ($d, $row) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'status_processo', 'dt' => 8),
    array('db' => 'objeto', 'dt' => 9),
    array('db' => 'data_vencimento_atualizada', 'dt' => 10, 'formatter' => function ($d, $row){
        return Functions::converteData($d);
    }),
    array('db' => 'apensado_id', 'dt' => 11)
);
// SQL server connection information
$config = AppController::getDatabaseConfig();
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
$where = 'id IS NOT NULL';
if (isset($_GET['is_arquivado'])) {
    $where .= " AND is_arquivado = {$_GET['is_arquivado']}";
}
if (isset($_GET['pesquisar'])) {
    if (!empty($_GET['exercicio'])) {
        $where .= " AND exercicio = {$_GET['exercicio']}";
    }
    if (!empty($_GET['numero_processo'])) {
        $where .= " AND numero = {$_GET['numero_processo']}";
    }
    if (!empty($_GET['interessado_id'])) {
        $where .= " AND interessado_id = {$_GET['interessado_id']}";
    }
    if (!empty($_GET['interessado_string'])) {
        $interessado_string = filter_input(INPUT_GET, 'interessado_string', FILTER_SANITIZE_SPECIAL_CHARS);
        $where .= " AND interessado like '%{$interessado_string}%' " ;
    }
    if (!empty($_GET['assunto_id'])) {
        $where .= " AND assunto_id = {$_GET['assunto_id']} OR assunto_pai_id = {$_GET['assunto_id']}";
    }
    if (!empty($_GET['assunto_string'])) {
        $assunto_string = filter_input(INPUT_GET, 'assunto_string', FILTER_SANITIZE_SPECIAL_CHARS);
        $where .= " AND assunto like '%{$assunto_string}%' ";
    }
    if (!empty($_GET['setor_origem_id'])) {
        $where .= " AND setor_origem_id = {$_GET['setor_origem_id']}";
    }
    if (!empty($_GET['origem'])) {
        $where .= " AND origem like '%{$_GET['origem']}%'";
    }
    if (!empty($_GET['setor_atual_id'])) {
        $where .= " AND setor_atual_id = {$_GET['setor_atual_id']}";
    }
    if (!empty($_GET['setor_atual_string'])) {
        $setor_atual_string = filter_input(INPUT_GET, 'setor_atual_string', FILTER_SANITIZE_SPECIAL_CHARS);
        $where .= " AND setor_atual like '%{$setor_atual_string}%'";
    }
    if (!empty($_GET['responsavel_abertura_id'])) {
        $where .= " AND usuario_abertura_id = {$_GET['responsavel_abertura_id']}";
    }
    if (!empty($_GET['status_id'])) {
        $where .= " AND status_id = {$_GET['status_id']}";
    }
    if (!empty($_GET['objeto'])) {
        $objeto = trim(filter_input(INPUT_GET, 'objeto', FILTER_SANITIZE_STRING));
        switch ($_GET['tipo_pesquisa_objeto']) {
            case 0:
                $where .= " AND objeto LIKE '%$objeto%'";
                break;
            case 1:
                $where .= " AND objeto LIKE '$objeto%'";
                break;
            case 2:
                $where .= " AND objeto LIKE '$objeto'";
                break;
        }
    }
    if (isset($_GET['data_abertura_fim']) && isset($_GET['data_abertura_ini'])) {
        $where .= getRangeSql('data_abertura', Functions::converteDataParaMysql($_GET['data_abertura_ini']), Functions::converteDataParaMysql($_GET['data_abertura_fim']));
    }
    if (isset($_GET['data_tramite_fim']) && isset($_GET['data_tramite_ini'])) {
        $where .= getRangeSql('data_envio', Functions::converteDataParaMysql($_GET['data_tramite_ini']), Functions::converteDataParaMysql($_GET['data_tramite_fim']));
    }
    if (isset($_GET['data_arquivamento_fim']) && isset($_GET['data_arquivamento_ini'])) {
        $where .= getRangeSql('data_arquivamento', Functions::converteDataParaMysql($_GET['data_arquivamento_ini']), Functions::converteDataParaMysql($_GET['data_arquivamento_fim']));
    }
    if (!empty($_GET['responsavel_atual_id'])) {
        $where .= " AND usuario_destino_id = {$_GET['responsavel_atual_id']}";
    }
    if (!empty($_GET['apensadoId'])){
        $processo = (new Processo())->buscar($_GET['apensadoId']);
        $where .= " AND id <> {$_GET['processoId']}";
        if (!empty($processo->getApensado())){
            $where .= " AND id <> {$_GET['apensadoId']} AND id <> {$processo->getApensado()->getId()}"; 
        }else{
            $where .= " AND id <> {$_GET['apensadoId']}";
        }
    }else if (!empty($_GET['processoId'])){
        $where .= " AND id <> {$_GET['processoId']}";
    }
} else if (isset($_GET['vencidos'])) {
    $exercicio = ProcessoController::getExercicioAtual();
    $where .= $exercicio != null ? " AND exercicio='$exercicio'" : "";
    $where .= " AND is_arquivado = 0 AND data_vencimento_tramite < NOW()";
    $usuario = UsuarioController::getUsuarioLogadoDoctrine();
    if ($usuario != null) {
        if (!in_array($usuario->getTipo(), [\App\Enum\TipoUsuario::ADMINISTRADOR, \App\Enum\TipoUsuario::MASTER])) {
            $where .= " AND setor_atual_id IN({$usuario->getSetoresIds(true)})";
            $where .= " AND IF(usuario_destino_id IS NOT NULL,usuario_destino_id={$usuario->getId()},1)=1";
        }
    }
}else if(isset($_GET["processos_vencidos"]) ){
    $exercicio = ProcessoController::getExercicioAtual();
    $where .= $exercicio != null ? " AND exercicio = '$exercicio'" : "";
    $where .= " AND is_arquivado=0 ";
    $where .= " AND ". ProcessoDao::sqlDataVencimentoAtualizada("view_processos")." < NOW()";
    $usuario = UsuarioController::getUsuarioLogadoDoctrine();
    if ($usuario != null) {
        if (!in_array($usuario->getTipo(), array(\App\Enum\TipoUsuario::ADMINISTRADOR, \App\Enum\TipoUsuario::MASTER))) {
            $where .= " AND setor_atual_id IN({$usuario->getSetoresIds(true)})";
            $where .= " AND IF(usuario_destino_id IS NOT NULL,usuario_destino_id={$usuario->getId()},1)=1";
        }
    } 
}
echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);

function getRangeSql($campo, $valorIni, $valorFim, $aspas = true)
{
    $sql = "";
    if (!empty($valorIni) && !empty($valorFim)) {
        $sql .= " AND DATE($campo) BETWEEN '$valorIni' AND '$valorFim'";
    } elseif (!empty($valorIni) && empty($valorFim)) {
        $sql .= " AND DATE($campo) >= '$valorIni'";
    } elseif (empty($valorIni) && !empty($valorFim)) {
        $sql .= " AND DATE($campo) <= '$valorFim'";
    }
    if (!$aspas) {
        $sql = str_replace("'", "", $sql);
    }
    return $sql;
}