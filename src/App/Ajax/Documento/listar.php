<?php

use App\Model\SituacaoDocumento;
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

/*
 * Query para criar view
  CREATE OR REPLACE VIEW view_documentos AS
  SELECT d.id as documento_id,d.situacao_id,d.numero,d.ano,d.data,d.isAprovado,d.isPublicado,d.isDestaque,d.isLido,d.arquivo,d.assunto,d.conteudo,d.textoOCR,
  t.id as tipo_id,t.nome as tipo,p.nomeParlamentar as parlamentarResponsavel,
  p2.numero as numero_protocolo,p2.data as data_protocolo,p2.hora as hora_protocolo,s.nome as situacao
  FROM documento d
  JOIN tipo_documento t ON t.id=d.tipo_id
  LEFT JOIN situacao_documento s ON s.id=d.situacao_id
  LEFT JOIN protocolo p2 ON p2.documento_id=d.id
  LEFT JOIN parlamentar p ON p.id=d.parlamentarResponsavel_id
  ORDER BY d.data ASC;
 */
// DB table to use
$table = 'view_documentos';

// Table's primary key
$primaryKey = 'documento_id';
$situacoesDocumento = (new SituacaoDocumento())->listar();
$columns = array(
    array('db' => 'tipo', 'dt' => 0),
    array('db' => 'numero', 'dt' => 1),
    array('db' => 'ano', 'dt' => 2),
    array('db' => 'data', 'dt' => 3,
        'formatter' => function( $d, $row ) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'parlamentarResponsavel', 'dt' => 4),
    array('db' => 'numero_protocolo', 'dt' => 5),
    array('db' => 'data_protocolo', 'dt' => 6,
        'formatter' => function( $d, $row ) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'hora_protocolo', 'dt' => 7),
    array('db' => 'isPublicado', 'dt' => 8,
        'formatter' => function( $d, $row) {
            $checked = $d ? 'checked' : '';
            $html = '<input id="Documento:isPublicado:' . $row[14] . '" class="auto-update" type="checkbox" name="is_publicado' . $row[13] . '" value="1" ' . $checked . '/>';
            return $html;
        }
    ),
    array('db' => 'isDestaque', 'dt' => 9,
        'formatter' => function( $d, $row) {
            $checked = $d ? 'checked' : '';
            $html = '<input id="Documento:isDestaque:' . $row[14] . '" class="auto-update" type="checkbox" name="is_destaque' . $row[13] . '" value="1" ' . $checked . '/>';
            return $html;
        }
    ),
    array('db' => 'isLido', 'dt' => 10,
        'formatter' => function( $d, $row) {
            $checked = $d ? 'checked' : '';
            $html = '<input id="Documento:isLido:' . $row[14] . '" class="auto-update" type="checkbox" name="is_lido' . $row[13] . '" value="1" ' . $checked . '/>';
            return $html;
        }
    ),
    array('db' => 'isAprovado', 'dt' => 11,
        'formatter' => function( $d, $row) {
            return $d ? 'Sim' : 'Não';
        }
    ),
    array('db' => 'situacao', 'dt' => 12,
        'formatter' => function( $d, $row) {
            $situacao = !empty($d) ? ucwords(mb_strtolower($d, 'UTF-8')) : '*não definida';
            return "<span class='badge badge-secondary'>$situacao</span>";
        }
    ),
    array('db' => 'documento_id', 'dt' => 13,
        'formatter' => function( $d, $row) {
            $html = '<div class="dropdown">
                            <button title="Ações" class="btn btn-outline-secondary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cog"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" title="Anexos" href="' . APP_URL_RESTRITO . 'anexo/index/Documento/' . $d . '"><i class="fa fa-file-text-o"></i> Anexos</a>
                                <a target="_blank" class="dropdown-item" title="Imprimir Projeto de Ato" href="' . APP_URL_RESTRITO . 'Documento/imprimir/id/' . $d . '"><i class="fa fa-print"></i> Imprimir</a>
                                <a class="dropdown-item" title="Editar" href="' . APP_URL_RESTRITO . 'Documento/editar/id/' . $d . '"><i class="fa fa-edit"></i> Editar</a>
                                <a class="dropdown-item btn-excluir" title="Excluir" href="' . APP_URL_RESTRITO . 'Documento/excluir/id/' . $d . '"><i class="fa fa-times"></i> Excluir</a>
                            </div>
                        </div>';
            return $html;
        }
    ),
    array('db' => 'documento_id', 'dt' => 14)
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
$where = " documento_id IS NOT NULL";
if (!empty($_GET['tipo_id'])) {
    $where .= " AND tipo_id=" . $_GET['tipo_id'];
}
if (!empty($_GET['texto'])) {
    $where .= " AND  MATCH (assunto,conteudo,textoOCR) AGAINST ('+\"" . filter_input(INPUT_GET, 'texto', FILTER_SANITIZE_STRING) . "\"' IN BOOLEAN MODE)";
}
echo json_encode(
        SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
);

