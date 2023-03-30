<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 07/12/2018
 * Time: 13:41
 */

use Core\Util\SSP;

include '../../../../bootstrap.php';

// DB table to use

/*$table = <<<EOT
(
    SELECT l.*,lf.descricao as local,t.descricao as tipo_local,sub.descricao as subtipo_local,
    u.nome as usuario_cadastro,ua.nome as usuario_alteracao,p.id as processo_id
    FROM localizacao_fisica l
    JOIN local_fisico lf ON lf.id=l.local_id
    JOIN tipo_local_fisico t ON t.id=l.tipo_local_id
    JOIN subtipo_local_fisico sub ON sub.id=l.subtipo_local_id
    LEFT JOIN usuario u ON u.id=l.usuario_id
    LEFT JOIN usuario ua ON ua.id=l.usuario_alteracao_id
    LEFT JOIN processo p ON p.local_fisico_id=l.id
) temp
EOT;*/
// Table's primary key
$table = "view_arquivo_fisico";
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'numero_documento', 'dt' => 1,
        'formatter' => function ($d, $row) {
            return !empty($d) ? $d : (new \App\Model\Processo())->buscar($row[17])->getNumero();
        }
    ),
    array('db' => 'exercicio_documento', 'dt' => 2,
        'formatter' => function ($d, $row) {
            return !empty($d) ? $d : (new \App\Model\Processo())->buscar($row[17])->getExercicio();
        }
    ),
    array('db' => 'data_documento', 'dt' => 3,
        'formatter' => function ($d, $row) {
            return !empty($d) ? \Core\Util\Functions::converteData($d) : (new \App\Model\Processo())->buscar($row[17])->getDataAbertura(true);
        }
    ),
    array('db' => 'local', 'dt' => 4,
        'formatter' => function ($d, $row) {
            return $d . (!empty($row[10]) ? "<br/><small class='text-muted'>Ref.:{$row[10]}</small>" : "");
        }
    ),
    array('db' => 'tipo_local', 'dt' => 5,
        'formatter' => function ($d, $row) {
            return $d . (!empty($row[11]) ? "<br/><small class='text-muted'>Ref.:{$row[11]}</small>" : "");
        }
    ),
    array('db' => 'subtipo_local', 'dt' => 6,
        'formatter' => function ($d, $row) {
            return $d . (!empty($row[12]) ? "<br/><small class='text-muted'>Ref.:{$row[12]}</small>" : "");
        }
    ),
    array('db' => 'data_cadastro', 'dt' => 7,
        'formatter' => function ($d, $row) {
            return \Core\Util\Functions::converteData($d) . (!empty($row[13]) ? "<br/><small class='text-muted'>por: {$row[13]}</small>" : "");
        }
    ),
    array('db' => 'ultima_alteracao', 'dt' => 8,
        'formatter' => function ($d, $row) {
            return \Core\Util\Functions::converteData($d, true) . (!empty($row[14]) ? "<br/><small class='text-muted'>por: {$row[14]}</small>" : "");
        }
    ),
    array('db' => 'id', 'dt' => 9,
        'formatter' => function ($d, $row) {
            $html = "<div class='btn-group'>";
            $html .= '<a  class="btn btn-info btn-editar-no-propagate btn-xs" title="Editar" href="' . APP_URL . 'localizacaoFisica/editar/id/' . $d . '"><i class="fa fa-edit"></i></a>';
            $html .= '<a class="btn btn-danger btn-xs btn-excluir" title="Excluir" href="' . APP_URL . 'localizacaoFisica/excluir/id/' . $d . '"><i class="fa fa-times"></i></a>';
            $html .= "</div>";
            return $html;
        }
    ),
    array('db' => 'ref_local', 'dt' => 10),
    array('db' => 'ref_tipo_local', 'dt' => 11),
    array('db' => 'ref_subtipo_local', 'dt' => 12),
    array('db' => 'usuario_cadastro', 'dt' => 13),
    array('db' => 'usuario_alteracao', 'dt' => 14),
    array('db' => 'ementa', 'dt' => 15),
    array('db' => 'observacao', 'dt' => 16),
    array('db' => 'processo_id', 'dt' => 17),
);
$config = \Core\Controller\AppController::getDatabaseConfig();
// SQL server connection information
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
$where = "id IS NOT NULL";
$exercicio = isset($_GET['exercicio']) ? $_GET['exercicio'] : null;
if ($exercicio != null) {
    $where .= " AND exercicio_documento=$exercicio";
}
if (isset($_GET['pesquisar'])) {
    $where .= getRangeSql('numero_documento', $_GET['numero_documento_ini'], $_GET['numero_documento_fim'], false);
    $where .= getRangeSql('data_documento', \Core\Util\Functions::converteDataParaMysql($_GET['data_documento_ini']), \Core\Util\Functions::converteDataParaMysql($_GET['data_documento_fim']));
    $where .= getRangeSql('data_cadastro', \Core\Util\Functions::converteDataParaMysql($_GET['data_cadastro_ini']), \Core\Util\Functions::converteDataParaMysql($_GET['data_cadastro_fim']));
    if (!empty($_GET['local'])) {
        $where .= " AND local='{$_GET['local']}'";
    }
    if (!empty($_GET['refLocal'])) {
        $where .= " AND ref_local='{$_GET['local']}'";
    }
    if (!empty($_GET['tipoLocal'])) {
        $where .= " AND tipo_local='{$_GET['tipoLocal']}'";
    }
    if (!empty($_GET['refTipoLocal'])) {
        $where .= " AND ref_tipo_local='{$_GET['refTipoLocal']}'";
    }
    if (!empty($_GET['subTipoLocal'])) {
        $where .= " AND subtipo_local='{$_GET['subTipoLocal']}'";
    }
    if (!empty($_GET['refTipoLocal'])) {
        $where .= " AND ref_subtipo_local='{$_GET['refsubTipoLocal']}'";
    }
    if (!empty($_GET['texto'])) {
        $buscador = $_GET['tipo_texto'] == 0 ? "'%{$_GET['texto']}%'" : ($_GET['tipo_texto'] == 1 ? "'{$_GET['texto']}%'" : "'{$_GET['texto']}'");
        switch ($_GET['ref_texto']) {
            case 0:
                break;
                $where .= " AND ementa LIKE $buscador || observacao LIKE $buscador";
            case 1:
                $where .= " AND ementa LIKE $buscador";
                break;
            case 2:
                $where .= " AND observacao LIKE $buscador";
                break;
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
        $sql .= " AND $campo BETWEEN '$valorIni' AND '$valorFim'";
    } elseif (!empty($valorIni) && empty($valorFim)) {
        $sql .= " AND $campo >= '$valorIni'";
    } elseif (empty($valorIni) && !empty($valorFim)) {
        $sql .= " AND $campo <= '$valorFim'";
    }
    if (!$aspas) {
        $sql = str_replace("'", "", $sql);
    }
    return $sql;
}
