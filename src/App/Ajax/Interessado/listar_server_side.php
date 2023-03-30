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
$table = 'view_interessados';

// Table's primary key
$primaryKey = 'id';
$columns = array(
    array('db' => 'id', 'dt' => 0),
    array('db' => 'nome', 'dt' => 1),
    array('db' => 'cpf', 'dt' => 2,
        'formatter' => function ($d, $row) {
            $cpf_cnpj = !empty($d) ? $d : $row[5];
            return \Core\Util\Functions::formatarCpfCnpj($cpf_cnpj);
        }
    ),
    array('db' => 'id', 'dt' => 3,
        'formatter' => function ($d, $row) {
            $html = "<div class='btn-group'>";
            $html .= '<a class="btn btn-warning btn-xs btn-converter" title="Converter " href="' . APP_URL . 'interessado/converter/id/' . $d . '"><i class="fa fa-share"></i></a>';
            $html .= '<a  class="btn btn-info btn-editar-no-propagate btn-xs" title="Editar" href="' . APP_URL . 'interessado/editar/id/' . $d . '"><i class="fa fa-edit"></i></a>';
            $html .= '<a class="btn btn-danger btn-xs btn-desativar" title="Desativar" href="' . APP_URL . 'interessado/desativar/id/' . $d . '"><i class="fa fa-times"></i></a>';
            $html .= "</div>";
            return $html;
        }
    ),
    array('db' => 'cpf', 'dt' => 4),
    array('db' => 'cnpj', 'dt' => 5)
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
if (isset($_GET['nome'])) {
    $busca = \Core\Util\Functions::sanitizeString($_GET['nome']);
    $where .= " AND shadow_nome LIKE '%$busca%'";
}
if (isset($_GET['cpf_cnpj'])) {
    $cpf_cnpj = str_replace('-', '', filter_var($_GET['cpf_cnpj'], FILTER_SANITIZE_NUMBER_INT));
    $where .= " AND (cpf LIKE '$cpf_cnpj%' OR cnpj LIKE '$cpf_cnpj%')";
}
$where .= " AND is_ativo = 1";
echo json_encode(
    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);