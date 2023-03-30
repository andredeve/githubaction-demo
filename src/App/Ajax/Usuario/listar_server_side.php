<?php

use Core\Controller\AppController;
use Core\Util\SSP;

include '../../../../bootstrap.php';

// DB table to use
$table = <<<SQL
(
    SELECT u.id, p.nome, u.is_ativo FROM usuario u JOIN pessoa p on p.id = u.pessoa_id
) temp
SQL;


// Table's primary key
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'nome', 'dt' => 1)
);

$config = AppController::getDatabaseConfig();
// SQL server connection information
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
$where = "1";

if (isset($_GET['nome'])) {
    $busca = $_GET['nome'];
    $where .= " AND nome LIKE '%$busca%'";
}

$where .= " AND is_ativo = 1";



$result = SSP::complex($_GET, $sql_details,$table, $primaryKey, $columns, null, $where);


$data = array_map(function ($item) {
    return ["id" => $item[0], "nome" => $item[1]];
}, $result["data"]);
$result["data"] = $data;
echo json_encode($result);