<?php /** @noinspection DuplicatedCode */

use App\Controller\UsuarioController;
use App\Enum\TipoUsuario;
use Core\Controller\AppController;
use Core\Util\Functions;
use Core\Util\SSP;

include '../../../../bootstrap.php';

$usuario = UsuarioController::getUsuarioLogadoDoctrine();
$table = "view_solicitacoes";
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'data', 'dt' => 1,
        'formatter' => function ($d, $row) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'solicitante', 'dt' => 2),
    array('db' => ['processo', 'processo_id'], 'dt' => 3, 'formatter' => function($d, $row) {
        return "<a target='_blank' href='" . APP_URL . "processo/editar/id/$d[1]' class='text-left'>$d[0]</a>";
    }),
    array('db' => "documento_anterior", 'dt' => 4),
    array('db' => 'tipo', 'dt' => 5,
        'formatter' => function ($d, $row) {
            if ($d == "Edição") {
                return "<p class='text-justify'><i class='text-info fa fa-pencil text-justify' style='font-size: 12px'></i> $d</p>";
            } else {
                return "<p class='text-justify'><i class='text-danger fa fa-close' style='font-size: 12px'></i> $d</p>";
            }
        }
    ),
    array('db' => 'status', 'dt' => 6),
    array('db' => 'id', 'dt' => 7,
        'formatter' => function ($d, $row) {
            $acoes = "<button onclick='visualizarSolicitacao($d)' data-solicitacao-id='$d' class='btn btn-outline-primary btn-solicitacao-visualizar' title='Visualizar'><i class='fa fa-search' style='font-size: 16px'></i></button>";
            if ($row[7] === 'Aprovado') {
                $acoes .= "<button disabled data-solicitacao-id='$d' class='btn btn-outline-success btn-solicitacao-aprovar mr-1 ml-1' title='Executar'><i class='fa fa-check' style='font-size: 16px'></i></button>";
            } else if ($row[7] === 'Recusado') {
                $acoes .= "<button disabled data-solicitacao-id='$d' class='btn btn-outline-danger btn-solicitacao-reprovar' title='Recusar'><i class='fa fa-close' style='font-size: 16px'></i></button>";
            } else {
                $acoes .= "<button onclick='aprovarSolicitacaoAnexo(this)' data-solicitacao-id='$d' class='btn btn-outline-success mr-1 ml-1 btn-solicitacao-aprovar' title='Executar'><i class='fa fa-check' style='font-size: 16px'></i></button>";
                $acoes .= "<button onclick='reprovarSolicitacaoAnexo(this)' data-solicitacao-id='$d' class='btn btn-outline-danger btn-solicitacao-reprovar' title='Recusar'><i class='fa fa-close' style='font-size: 16px'></i></button>";
            }
            return $acoes;
        }
    )
);
// SQL server connection information
$config = AppController::getDatabaseConfig();
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
$tipoUsuario = $usuario->getTipo();
if ($tipoUsuario === TipoUsuario::ADMINISTRADOR || $tipoUsuario === TipoUsuario::MASTER) {
    $where = '1';
} else {
    $where = "solicitante_id = {$usuario->getId()}";
}
echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);