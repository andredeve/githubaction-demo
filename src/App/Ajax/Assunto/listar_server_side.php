<?php
use App\Enum\TipoUsuario;
use App\Controller\UsuarioController;

use Core\Util\SSP;

include '../../../../bootstrap.php';

// DB table to use
$table = 'assunto';

// Table's primary key
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'nome', 'dt' => 1,
        'formatter' => function ($d, $row) {
            return $d;
        }
    ),
    array('db' => 'prazo', 'dt' => 2,
        'formatter' => function ($d, $row) {
            return $d . " dia(s)";
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
if(UsuarioController::getUsuarioLogadoDoctrine()->getTipo() == TipoUsuario::INTERESSADO){
    $where = "  is_ativo=1";
    $where .= " AND is_externo=1 ";
}else{
    $where = " assunto_pai_id IS NULL AND is_ativo=1";
}
//$usuario_logado = \App\Controller\UsuarioController::getUsuarioLogadoDoctrine();
//if ($usuario_logado->getTipo() == \App\Enum\TipoUsuario::USUARIO) {
//}
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $where .= " AND id=" . $_GET['id'];
}
if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $busca = \Core\Util\Functions::sanitizeString($_GET['nome']);
    $where .= " AND shadow_nome LIKE '%$busca%'";
}
//echo $where;
echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);

