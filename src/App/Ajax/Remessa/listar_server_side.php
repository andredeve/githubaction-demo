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
$table = 'remessa';

// Table's primary key
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'horario', 'dt' => 1,
        'formatter' => function ($d, $row) {
            return Date('d/m/Y', strtotime($d));
        }
    ),
    array('db' => 'horario', 'dt' => 2,
        'formatter' => function ($d, $row) {
            return Date('H:i:s', strtotime($d));
        }
    ),
    array('db' => 'setor_origem', 'dt' => 3),
    array('db' => 'responsavel_origem', 'dt' => 4),
    array('db' => 'setor_destino', 'dt' => 5),
    array('db' => 'responsavel_destino', 'dt' => 6),
);
$config = \Core\Controller\AppController::getDatabaseConfig();
// SQL server connection information
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
$where = "1";
if (isset($_GET['cpf_cnpj'])) {
    $cpf_cnpj = str_replace('-', '', filter_var($_GET['cpf_cnpj'], FILTER_SANITIZE_NUMBER_INT));
    $where .= " AND (cpf LIKE '$cpf_cnpj%' OR cnpj LIKE '$cpf_cnpj%')";
}
echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);

