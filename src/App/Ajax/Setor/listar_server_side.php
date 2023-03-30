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
$table = 'setor';

// Table's primary key
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'orgao', 'dt' => 1,
        'formatter' => function ($d, $row) {
            $setor = new \App\Model\Setor();
            $setor = $setor->buscar($row['id']);
            
            return $setor->getOrgao();
        }
    ),
    array('db' => 'unidade', 'dt' => 2,
        'formatter' => function ($d, $row) {
            return $d;
        }
    ),
    array('db' => 'nome', 'dt' => 3,
        'formatter' => function ($d, $row) {
            return $d;
        }
    )
);
$config = \Core\Controller\AppController::getDatabaseConfig();
// SQL server connection information
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
//$where = " assunto_pai_id IS NULL";
//$usuario_logado = \App\Controller\UsuarioController::getUsuarioLogadoDoctrine();
//if ($usuario_logado->getTipo() == \App\Enum\TipoUsuario::USUARIO) {
$where = "  is_ativo=1";
//}
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $where .= " AND id=" . $_GET['id'];
}
if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $busca = $_GET['nome'];
    $where .= " AND nome LIKE '%$busca%'";
}
if (isset($_GET['orgao']) && !empty($_GET['orgao'])) {
    $busca = $_GET['orgao'];
    $where .= " AND orgao = '$busca'";
}
if (isset($_GET['unidade']) && !empty($_GET['unidade'])) {
    $busca = $_GET['unidade'];
    $where .= " AND unidade = '$busca'";
}
//echo $where;
echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);

