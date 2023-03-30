<?php

use Core\Controller\AppController;
use Core\Util\Functions;
use Core\Util\SSP;

include '../../../../bootstrap.php';

// DB table to use
$table = 'view_processos';
// Table's primary key
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'numero', 'dt' => 1),
    array('db' => 'exercicio', 'dt' => 2),
    array('db' => 'data_abertura', 'dt' => 3,
        'formatter' => function ($d, $row) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'assunto', 'dt' => 4),
    array('db' => 'interessado', 'dt' => 5),
    array('db' => 'setor_atual', 'dt' => 6),
    array('db' => 'status_processo', 'dt' => 7),
    array('db' => 'objeto', 'dt' => 8)
);
// SQL server connection information
$config = AppController::getDatabaseConfig();
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
$where = "numero IS NOT NULL 
    AND sigilo IN( '".App\Enum\SigiloProcesso::SEM_RESTRICAO."', '".App\Enum\SigiloProcesso::ANEXOS_SIGILOSOS."'  )";
if (!empty($_GET['anoProcesso'])) {
    $where .= ' AND exercicio=' . $_GET['anoProcesso'];
}
if (!empty($_GET['numeroProcesso'])) {
    $where .= ' AND numero=' . $_GET['numeroProcesso'];
}
if (!empty($_GET['interessadoProcesso'])) {
    $where .= " AND interessado_id=" . $_GET['interessadoProcesso'];
}
if (!empty($_GET['objetoProcesso'])) {
    $objeto = trim(filter_input(INPUT_GET, 'objetoProcesso', FILTER_SANITIZE_STRING));
    $where .= " AND objeto LIKE '%" . $objeto . "%'";
    //$where .= " AND  MATCH (objeto) AGAINST ('+\"" . filter_input(INPUT_GET, 'objeto', FILTER_SANITIZE_STRING) . "\"' IN BOOLEAN MODE)";
}
$tipo_id_ci = 136;
if (!empty($_GET['numeroCI'])) {
    $where .= " AND " . $_GET['numeroCI'] . " IN (SELECT SUBSTRING_INDEX(a.numero,'/',1) FROM anexo a WHERE  a.tipo_anexo_id=$tipo_id_ci AND a.processo_id=processo_id_view)";
}
if (!empty($_GET['anoCI'])) {
    $where .= " AND  " . $_GET['anoCI'] . "  IN (SELECT YEAR(a2.data) FROM anexo a2 WHERE a2.tipo_anexo_id=$tipo_id_ci AND a2.processo_id=processo_id_view)";
}
//echo $where;
echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);
