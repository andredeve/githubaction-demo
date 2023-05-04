<?php

use App\Controller\ProcessoController;
use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use App\Model\Processo;
use App\Model\Usuario;
use Core\Controller\AppController;
use Core\Util\Functions;
use Core\Util\SSP;

include '../../../../bootstrap.php';

// DB table to use
/*
$table = <<<EOT
(
    SELECT p.*,CONCAT(p.numero,"/",p.exercicio) as processo,so.nome as setor_origem,i.cpf,i.cnpj,i.nome as interessado,
    ua.nome as usuario_abertura,a.nome as assunto,st.cor as cor_status,st.descricao as status_processo,
    sant.nome as setor_anterior,t.setor_atual_id,sa.nome as setor_atual,t.is_despachado,t.data_vencimento as data_vencimento_tramite,
    t.id as tramite_id,t.remessa_idt.usuario_destino_id,t.usuario_envio_id,t.parecer,t.is_recebido,t.data_envio,f.id as fluxograma_id
    FROM processo p
    JOIN assunto a ON a.id=p.assunto_id
    JOIN tramite t ON t.processo_id=p.id AND t.numero_fase=p.numero_fase AND  t.assunto_id=p.assunto_id AND t.is_cancelado=0
    LEFT JOIN setor sant ON sant.id=t.setor_anterior_id
    JOIN status_processo st ON st.id=t.status_id
    LEFT JOIN fluxograma f ON f.assunto_id=a.id
    JOIN interessado i ON i.id=p.interessado_id
    JOIN usuario ua ON ua.id=p.usuario_abertura_id
    JOIN setor so ON so.id=p.setor_origem_id
    JOIN setor sa ON sa.id=t.setor_atual_id
) temp
EOT;
*/
$table = "view_processos";
$parametros = App\Controller\IndexController::getParametosConfig();
// Table's primary key
$primaryKey = 'id';
$col_is_sigiloso = $_GET['tipo_listagem'] == 'enviados' ? 14 : 13;
$col_is_recebido = $col_is_sigiloso + 1;
$col_cor_status = $col_is_recebido + 1;
$columns = array(array('db' => 'id', 'dt' => 0));
switch ($_GET['tipo_listagem']) {
    case 'enviados':
        $col_status_assinatura = 12;
        $col_cor_status = 15;
        $pre_columns = array(
            array('db' => 'tramite_id', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    global $parametros;
                    $disabled_cancelar = $row["is_recebido"] ? "disabled" : "";

                    return "<input type=\"checkbox\" name=\"cancelar_tramite[]\" title='Clique para selecionar o ".lcfirst($parametros['nomenclatura'])."' value=\"{$d}\" $disabled_cancelar/>";
                }
            )
        );
        $action_column = array(
            array('db' => 'fluxograma_id', 'dt' => 13,
                'formatter' => function ($d, $row) {
                    $show_cancelar = !$row["is_recebido"] && empty($d);
                    return criarBotoesAcoesProcesso($row["processo"], $row["setor_atual"], $row["id"], $row["tramite_id"], false, false, $show_cancelar, false);
                }
            )
        );
        break;
    case 'receber':
        $col_status_assinatura = 11;
        $pre_columns = array(
            array('db' => 'tramite_id', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    return "<input class=\"check-receber-processo\" type=\"checkbox\" name=\"receber_processo[]\" value=\"{$d}\"/>";
                }
            )
        );
        $action_column = array(
            array('db' => 'id', 'dt' => 12,
                'formatter' => function ($d, $row) {
                    return criarBotoesAcoesProcesso($row["processo"], $row["setor_atual"], $row["id"], $row["tramite_id"], true);
                }
            )
        );
        break;
    case 'contribuintes':
        $col_status_assinatura = 11;
        $pre_columns = array(
            array('db' => 'tramite_id', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    return "<input class=\"check-receber-processo\" type=\"checkbox\" name=\"receber_processo[]\" value=\"{$d}\"/>";
                }
            )
        );
        $action_column = array(
            array('db' => 'id', 'dt' => 12,
                'formatter' => function ($d, $row) {
                    return criarBotoesAcoesProcesso($row["processo"], $row["setor_atual"], $row["id"], $row["tramite_id"]);
                }
            )
        );
        break;
    case 'abertos':
        $col_status_assinatura = 11;
        $pre_columns = array(
            array('db' => 'tramite_id', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    return "<input class=\"check-arquivar-processo\" type=\"checkbox\" name=\"tramite_id_sel[]\" value=\"{$d}\"/>";
                }
            )
        );
        $action_column = array(
            array('db' => 'id', 'dt' => 12,
                'formatter' => function ($d, $row) {
                    return criarBotoesAcoesProcesso($row["processo"], $row["setor_atual"], $row["id"], $row["tramite_id"], false, true, false, true, true, true);
                }
            )
        );
        break;
    case 'arquivados':
        $pre_columns = array(
            array('db' => 'tramite_id', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    return "<input class=\"check-desarquivar-processo\" type=\"checkbox\" name=\"desarquivar_processo[]\" value=\"{$row["id"]}\"/>";
                }
            )
        );
        $col_status_assinatura = 10;
        $action_column = array(
            array('db' => 'id', 'dt' => 11,
                'formatter' => function ($d, $row) {
                    return criarBotoesAcoesProcesso($row["processo"], $row["setor_atual"], $row["id"], $row["tramite_id"]);
                }
            )
        );
        break;
    case 'vencidos':
        $pre_columns = $action_column = array();
        break;
}
if ($_GET['tipo_listagem'] != "vencidos") {
    $mid_columns = array(
        array('db' => 'processo', 'dt' => 2),
        array('db' => 'assunto', 'dt' => 3),
        array('db' => 'interessado', 'dt' => 4),
        array('db' => 'setor_anterior', 'dt' => 5,
            'formatter' => function ($d, $row) {
                global $col_cor_status;
                $indice_setor_anterior = $col_cor_status + 1;
                return !empty($d) ? $d : $row[$indice_setor_anterior];
            }
        ),
        array('db' => 'setor_atual', 'dt' => 6),
        array('db' => 'data_envio', 'dt' => 7,
            'formatter' => function ($d, $row) {
                return Functions::converteData($d);
            }
        ),
        array('db' => ($_GET['tipo_listagem'] == 'arquivados' ? 'data_arquivamento' : 'data_vencimento_tramite'), 'dt' => 8,
            'formatter' => function ($d, $row) {
                return Functions::converteData($d);
            }
        ),
        array('db' => 'data_vencimento_atualizada', 'dt' => 9,
            'formatter' => function ($d, $row) {
                return Functions::converteData($d);
            }
        ),
        array('db' => 'status_processo', 'dt' => 10,
            'formatter' => function ($d, $row) {
                return criarBotaoStatus($row["tramite_id"], $row["cor_status"], $d, $row["processo"]);
            }
        )
    );
    if ($_GET['tipo_listagem'] == 'enviados') {
        $mid_columns[] = array('db' => 'is_recebido', 'dt' => 11,
            'formatter' => function ($d, $row) {
                if ($d) {
                    //$info_recebimento = "Horário: {$tramite->getDataRecebimento()->format('d/m/Y - H:i')}\nUsuário: {$tramite->getUsuarioRecebimento()->getNome()}";$disabled_cancelar = "disabled";
                    $info_recebimento = "";
                    $label_recebido = "<label title=\"$info_recebimento\" style=\"cursor:help\" class=\"badge badge-success \"><i class=\"fa fa-check\"></i> Sim</label>";
                } else {
                    $label_recebido = "<label class=\"badge badge-danger \"><i class=\"fa fa-times\"></i> Não</label>";

                }
                return $label_recebido;
            }
        );
    }
} else {
    $mid_columns = array();
}

$post_columns = array(
    array('db' => 'sigilo', 'dt' => $col_is_sigiloso),
    array('db' => 'is_recebido', 'dt' => $col_is_recebido),
    array('db' => 'cor_status', 'dt' => $col_cor_status),
    array('db' => 'setor_origem', 'dt' => $col_cor_status + 1),
    array('db' => 'remessa_id', 'dt' => $col_cor_status + 2),
);

array_push(
    $mid_columns,
    array('db' => 'id', 'dt' => $col_status_assinatura,
        'formatter' => function ($d) {
            return "<div data-processo-id='$d'><img style='width: 30px' src='" . APP_URL . "assets/img/loading.svg'></div>";
        }
    ),
    array('db' => 'objeto', 'dt' => $col_cor_status + 3)
);
$columns = array_merge($columns, $pre_columns, $mid_columns, $action_column, $post_columns);

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
$exercicio = App\Controller\ProcessoController::getExercicioAtual();
$usuario = isset($_GET['usuario_logado_id']) ? (new Usuario())->buscar($_GET['usuario_logado_id']) : UsuarioController::getUsuarioLogadoDoctrine();
if ($exercicio != null) {
    $where .= " AND exercicio=$exercicio";
}
if($_GET['tipo_listagem'] != 'contribuintes'){
    $where .= " AND numero IS NOT NULL ";
}
switch ($_GET['tipo_listagem']) {
    case 'enviados':
        $where .= " AND usuario_envio_id={$usuario->getId()}";
        break;
    case 'receber':
        $where .= " AND is_arquivado=0 AND ( is_recebido=0 or is_recebido is null )";
        break;
    case 'contribuintes':
        $where .= " AND is_arquivado=0 AND is_recebido=0 AND  numero is null";
        break;
    case 'abertos':
        $where .= " AND is_arquivado=0 AND is_recebido=1";
        break;
    case 'arquivados':
        $where .= " AND is_arquivado=1";
        break;
    case 'vencidos':
        $where .= " AND is_arquivado=0 AND data_vencimento_tramite<NOW()";
        break;
}
//$where .= " AND apensado_id IS NULL ";
if ($usuario != null && $_GET['tipo_listagem'] != 'enviados' && !$usuario->isAdm()) {
    $setores = $usuario->getSetoresIds(true);
    
    $where .= !empty($setores)? " AND (setor_atual_id IN({$setores}) OR setor_atual_id IS NULL) ":"";
    $where .= " AND IF(usuario_destino_id IS NOT NULL,usuario_destino_id={$usuario->getId()},1)=1";
}
echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);

/**
 * Criar botões de ação do processo
 * @param      $processo
 * @param      $setor_atual
 * @param      $processo_id
 * @param      $tramite_id
 * @param bool $receber
 * @param bool $tramitar
 * @param bool $cancelar
 * @param bool $arquivar
 * @param bool $devolver
 * @param bool $recusar
 * @return string
 */
function criarBotoesAcoesProcesso($processo, $setor_atual, $processo_id, $tramite_id, $receber = false, $tramitar = false, $cancelar = false, $arquivar = false, $devolver = false, $recusar = false)
{
    $app_url = APP_URL;
    $button = "<div class=\"btn-group dropleft\">"
        . "<button title=\"Ações disponíveis\" type=\"button\" class=\"btn btn-light border btn-xs dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">"
        . "  <i class=\"fa fa-cogs\"></i>"
        . "</button>"
        . " <div class=\"dropdown-menu\">"
        . "<a class=\"dropdown-item\" title=\"Visualizar Processo Digital\" target=\"_blank\" href=\"{$app_url}src/App/View/Processo/visualizar_digital.php?processo_id={$processo_id}\"><i class=\"fa fa-search\"></i> Processo Digital</a>";
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
    $button .= " <a class=\"dropdown-item btn-loading\" title=\"Editar\" href=\"{$app_url}processo/editar/id/{$processo_id}\"><i class=\"fa fa-edit\"></i> Editar</a>"
        . "<a processo=\"$processo\" class=\"dropdown-item btn-excluir-processo\" title=\"Excluir\" href=\"{$app_url}processo/excluir/id/{$processo_id}\"><i class=\"fa fa-trash-o\"></i> Excluir</a>"
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
function criarBotaoStatus($tramite_id, $cor_status, $status, $processo)
{
    $disabled = $_GET['tipo_listagem'] == 'arquivados' ? 'disabled' : '';
    if (empty($cor_status)) {
        return "<button tramite_id=\"{$tramite_id}\" processo=\"$processo\" title=\"Clique para alterar status\" type=\"button\" style=\"background-color: {$cor_status}\" class=\"btn btn-xs btn-light btn-alterar-status btn-block\" $disabled><small>{$status}</small></button>";
    }
    return "<button tramite_id=\"{$tramite_id}\" processo=\"$processo\" title=\"Clique para alterar status\" type=\"button\" style=\"background-color: {$cor_status}\" class=\"btn btn-xs btn-alterar-status btn-block\" $disabled><small>{$status}</small></button>";
}