<?php

use Core\Controller\AppController;
use Core\Util\Functions;
use Core\Util\SSP;

include '../../../../bootstrap.php';
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */
// DB table to use
$table = 'view_documentos';

// Table's primary key
$primaryKey = 'documento_id';

$columns = array(
    array('db' => 'tipo', 'dt' => 0,
        'formatter' => function( $d, $row ) {
            $data = Functions::converteData($row[3]);
            $html = '<dl class="row">';
            $html .= '<dt class="col-sm-2">Tipo</dt>';
            $html .= '<dd class="col-sm-10">' . $row[0] . '</dd>';
            $html .= '<dt class="col-sm-2">Número</dt>';
            $html .= '<dd class="col-sm-10">' . $row[1] . '/' . $row[2] . '</dd>';
            $html .= '<dt class="col-sm-2">Data</dt>';
            $html .= '<dd class="col-sm-10">' . $data . '</dd>';
            $html .= '<dt class="col-sm-2">Autor</dt>';
            $html .= '<dd class="col-sm-10">' . $row[4] . '</dd>';
            $html .= '<dt class="col-sm-2">Assunto</dt>';
            $html .= '<dd class="col-sm-10 text-muted">' . $row[5] . '</dd>';
            $html .= "</dl>";
            return $html;
        }
    ),
    array('db' => 'numero', 'dt' => 1),
    array('db' => 'ano', 'dt' => 2),
    array('db' => 'data', 'dt' => 3,
        'formatter' => function( $d, $row ) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'parlamentarResponsavel', 'dt' => 4),
    array('db' => 'assunto', 'dt' => 5),
    array('db' => 'documento_id', 'dt' => 6,
        'formatter' => function( $d, $row) {
            $html = "<a target='_blank' href='" . APP_URL_ROOT . "imprimirDocumento/id/" . $row[7] . "' class='btn  btn-sm btn-danger' title='Gerar documento em PDF'><i class='fa fa-file-pdf-o'></i></a>";
            return $html;
        }
    ),
    array('db' => 'documento_id', 'dt' => 7)
);
$config = AppController::getDatabaseConfig();
// SQL server connection information
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$where = " isPublicado=1";
if (!empty($_GET['tipo_filter'])) {
    $where .= " AND tipo_id=" . $_GET['tipo_filter'];
}
if (!empty($_GET['ano_filter'])) {
    $where .= " AND ano='" . $_GET['ano_filter'] . "'";
}
if (!empty($_GET['numero_filter'])) {
    $where .= " AND numero=" . $_GET['numero_filter'];
}
if (!empty($_GET['responsavel_filter'])) {
    $where .= " AND responsavel_id=" . $_GET['responsavel_filter'];
}
if (!empty($_GET['texto_filter'])) {
    $where .= " AND  MATCH (assunto,conteudo,textoOCR) AGAINST ('+\"" . filter_input(INPUT_GET, 'texto_filter', FILTER_SANITIZE_STRING) . "\"' IN BOOLEAN MODE)";
}
echo json_encode(
        SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);

