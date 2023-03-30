<?php

use App\Controller\ProcessoController;
use App\Controller\IndexController;
use App\Controller\UsuarioController;
use App\Model\Processo;
use Core\Controller\AppController;
use Core\Util\Functions;
use Core\Util\SSP;

include '../../../../bootstrap.php';

$table = "view_processos";
$parametros = IndexController::getParametosConfig();
// Table's primary key
$primaryKey = 'id';
$position = 0;
$columns = array(
    array('db' => 'id', 'dt' => $position++),
    array('db' => 'processo', 'dt' => $position++),
    array('db' => 'assunto', 'dt' => $position++),
    array('db' => 'data_abertura', 'dt' => $position++,
        'formatter' => function ($d, $row) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'status_processo', 'dt' => $position++,
        'formatter' => function($d, $row){
            return '<span class="badge" style="background-color:'.$row['cor_status'].'">'.$row["status_processo"].'</span>';
        }
    ),
    array('db' => 'setor_atual', 'dt' => $position++),
    array('db' => 'id', 'dt' => $position++,
        'formatter' => function ($d, $row) {
            return criarBotoesAcoesProcesso($row["processo"], $row["setor_atual"], $row["id"], $row["tramite_id"]);
        }
    ),
    array('db' => 'sigilo', 'dt' => $position++),
    array('db' => 'is_recebido', 'dt' => $position++),
    array('db' => 'cor_status', 'dt' => $position++),
    array('db' => 'setor_origem', 'dt' => $position++),
    array('db' => 'remessa_id', 'dt' => $position++),
    array('db' => 'tramite_id', 'dt' => $position++),
    array('db' => 'objeto', 'dt' => $position++)
);


// SQL server connection information
$config = AppController::getDatabaseConfig();
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
$where = "id IS NOT NULL";
//Filtro comum
$interessado = UsuarioController::getUsuarioLogadoDoctrine()->getPessoa()->getInteressados()[0];

$where .= " AND interessado_id={$interessado->getId()}";

echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);

/**
 * Criar botões de ação do processo
 * @param $processo
 * @param $setor_atual
 * @param $processo_id
 * @param $tramite_id
 * @param bool $tramitar
 * @param bool $receber
 * @param bool $cancelar
 * @param bool $arquivar
 * @param bool $devolver
 * @param bool $recusar
 * @return string
 */
function criarBotoesAcoesProcesso($processo, $setor_atual, $processo_id, $tramite_id, bool $tramitar = false, bool $receber = false, bool $cancelar = false, bool $arquivar = false, bool $devolver = false, bool $recusar = false): string
{
    $app_url = APP_URL;
    $button = "<div class=\"btn-group dropleft\">"
        . "<button title=\"Ações disponíveis\" type=\"button\" class=\"btn btn-light border btn-xs dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">"
        . "  <i class=\"fa fa-cogs\"></i>"
        . "</button>"
        . " <div class=\"dropdown-menu\">";
    if (!empty($processo)) {
        $button .= "<a class=\"dropdown-item\" title=\"Visualizar Processo Digital\" target=\"_blank\" href=\"{$app_url}src/App/View/Processo/visualizar_digital.php?processo_id={$processo_id}\"><i class=\"fa fa-search\"></i> Processo Digital</a>";
    }
    if ($tramitar) {
        $button .= "<a tramite_id=\"{$tramite_id}\" processo=\"$processo\" class=\"dropdown-item btn-tramitar-processo\" title=\"Tramitar Processo\" href=\"javascript:\"><i class=\"fa fa-send-o\"></i> Tramitar</a>";
    }
    if ($devolver) {
        $button .= "<a tramite_id=\"{$tramite_id}\" processo=\"$processo\" class=\"dropdown-item btn-devolver-processo\" title=\"Devolver à Origem\" href=\"javascript:\"><i class=\"fa fa-reply\"></i> Devolver à Origem</a>";
    }
    if($recusar){
        $button .= "<a tramite_id=\"{$tramite_id}\" processo=\"$processo\" class=\"dropdown-item btn-recusar-processo\" title=\"Recusar Processo\" href=\"javascript:\"><i class=\"fa  fa-times\"></i> Recusar</a>";
    }
    if ($cancelar) {
        $button .= "<a tramite_id=\"{$tramite_id}\"  class=\"dropdown-item btn-cancelar-envio\" title=\"Cancelar Trâmite\" href=\"javascript:\"><i class=\"fa fa-times\"></i> Cancelar envio</a>";
    }
    if ($receber) {
        $button .= "<a setor_atual=\"".$setor_atual."\" processo=\"$processo\" class=\"dropdown-item btn-receber-processo\" title=\"Receber Processo\" href=\"{$app_url}tramite/receber/id/{$tramite_id}/\"><i class=\"fa fa-check\"></i> Receber</a>";
    }
    if ($arquivar) {
        $button .= "<a processo_id=\"{$processo_id}\" processo=\"$processo\" class=\"dropdown-item btn-arquivar-processo\" title=\"Arquivar Processo\" href=\"{$app_url}tramite/tramitar/id/{$tramite_id}/\"><i class=\"fa fa-folder-open-o\"></i> Arquivar</a>";
    }
    $button .= " <a class=\"dropdown-item btn-loading\" title=\"Editar\" href=\"{$app_url}contribuinte/editar/id/{$processo_id}\"><i class=\"fa fa-edit\"></i> Editar</a>"
        . "<a processo=\"$processo\" class=\"dropdown-item btn-excluir-processo\" title=\"Excluir\" href=\"{$app_url}contribuinte/excluir/id/{$processo_id}\"><i class=\"fa fa-trash-o\"></i> Excluir</a>"
        . "</div>"
        . "</div>";
    return $button;
}

/**
 * Criar botão para alterar status do processo
 * @param $tramite_id
 * @param $cor_status
 * @param $status
 * @param $processo
 * @return string
 */
function criarBotaoStatus($tramite_id, $cor_status, $status, $processo): string
{
    $disabled = $_GET['tipo_listagem'] == 'arquivados' ? 'disabled' : '';
    if (empty($cor_status)) {
        return "<button tramite_id=\"{$tramite_id}\" processo=\"$processo\" title=\"Clique para alterar status\" type=\"button\" style=\"background-color: {$cor_status}\" class=\"btn btn-xs btn-light btn-alterar-status btn-block\" $disabled><small>{$status}</small></button>";
    }
    return "<button tramite_id=\"{$tramite_id}\" processo=\"$processo\" title=\"Clique para alterar status\" type=\"button\" style=\"background-color: {$cor_status}\" class=\"btn btn-xs btn-alterar-status btn-block\" $disabled><small>{$status}</small></button>";
}

/**
 * @param $id_processo
 * @return string
 */
function buscarStatusAssinaturas($id_processo): string
{
    $lxSignAnexosIds = (new Processo)->buscarLxSignIdDosAnexos($id_processo);
    if (!empty($lxSignAnexosIds)) {
        $anexosStatusTemp = ProcessoController::buscarStatusAssinaturas($lxSignAnexosIds);
        $semAssinatura = 0;
        foreach ($anexosStatusTemp as $assinatura) {
            if (($assinatura->status != 'Finalizado')) {
                $semAssinatura = 1;
                break;
            }
        }
        $statusAssinatura = ($semAssinatura > 0)
            ? '<i data-toggle="tooltip" data-placement="top" title="Pendente de assinatura(s)." class="fa fa-exclamation-circle text-warning" style="font-size: 1.2rem;"></i>'
            : '<i data-toggle="tooltip" data-placement="top" title="Totalmente assinado." class="fa fa-edit text-success" style="font-size: 1.2rem;"></i>';
        //pendente e finalizado
    } else {
        $statusAssinatura = '<i class="fa fa-check-circle-o text-info" data-toggle="tooltip" data-placement="top" title="Sem requisição de assinatura(s)." style="font-size: 1.2rem;"></i>';//não necessita de assinatura
    }
    return $statusAssinatura;
}