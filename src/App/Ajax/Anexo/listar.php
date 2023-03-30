<?php

use Core\Controller\AppController;
use Core\Util\Functions;
use Core\Util\SSP;

include '../../../../bootstrap.php';

/*$table = <<<EOT
(
    SELECT a.*,t.descricao as tipo,CONCAT(p.numero,"/",p.exercicio) as processo
    FROM anexo a
    JOIN processo p ON p.id=a.processo_id
    JOIN tipo_anexo t ON t.id=a.tipo_anexo_id
) temp
EOT;*/
$table = "view_anexos";
$primaryKey = 'id';
$columns = array(
    array('db' => 'processo_id', 'dt' => 0),
    array('db' => 'id', 'dt' => 1),
    array('db' => 'processo', 'dt' => 2,
        'formatter' => function ($d, $row) {
            return "<a onclick='visualizarProcesso({$row[0]})' href='#'>$d</a>";
        }
    ),
    array('db' => 'tipo', 'dt' => 3),
    array('db' => 'numero', 'dt' => 4,
        'formatter' => function ($d, $row) {
            return utf8_encode($d);
        }
    ),
    array('db' => 'exercicio', 'dt' => 5),
    array('db' => 'data', 'dt' => 6,
        'formatter' => function ($d, $row) {
            return Functions::converteData($d);
        }
    ),
    array('db' => 'valor', 'dt' => 7,
        'formatter' => function ($d, $row) {
            return Functions::decimalToReal($d);
        }
    ),
    array('db' => 'qtde_paginas', 'dt' => 8),
    array('db' => 'id', 'dt' => 9,
        'formatter' => function ($d, $row) {
            $anexo = (new \App\Model\Anexo())->buscar($d);
            $arquivo = $anexo->getArquivo();
            if (!empty($arquivo)) {
                $file = $anexo->getPath() . $arquivo;
                $file_url = $anexo->getPathUrl() . $arquivo;
                $extensao = strtolower(pathinfo($file)['extension']);
                if ($extensao == 'pdf') {
                    return '<a target="_blank" href="' . $file_url . '"
                           class="btn btn-xs btn-warning"><i class="fa fa-search"></i></a>';
                } elseif ($anexo->isImage()) {
                    return '<a data-title="' . $anexo . '" data-lightbox="anexo_' . $d . '"
                           href="' . $file_url . '" class="btn btn-xs btn-warning"><i
                                    class="fa fa-search"></i></a>';
                } else {
                    return "<a class='btn btn-warning btn-xs' title='Visualizar Arquivos' target='_blank'
                           href='" . APP_URL . "src/App/View/Anexo/visualizar_digitalizados.php?anexo_id={$anexo->getId()}&indice=0&imagens={$anexo->getImagens(true)}'><i
                                    class='fa fa-search'></i></a>";
                }
            }
            return "";
        }
    ),
    array('db' => 'descricao', 'dt' => 10)
);
// SQL server connection information
$config = AppController::getDatabaseConfig();
$sql_details = array(
    'user' => $config['db_user'],
    'pass' => $config['db_password'],
    'db' => $config['db_name'],
    'host' => $config['db_host']
);
$where = '1';
if (isset($_GET['pesquisar'])) {
    if (!empty($_GET['tipo_anexo_id'])) {
        $where .= " AND tipo_anexo_id=" . $_GET['tipo_anexo_id'];
    }
    $where .= getRangeSql('data', \Core\Util\Functions::converteDataParaMysql($_GET['data_anexo_ini']), \Core\Util\Functions::converteDataParaMysql($_GET['data_anexo_fim']));
    $where .= getRangeSql('data_cadastro', \Core\Util\Functions::converteDataParaMysql($_GET['data_upload_ini']), \Core\Util\Functions::converteDataParaMysql($_GET['data_upload_fim']));
    $where .= getRangeSql('qtde_paginas', $_GET['qtde_paginas_ini'], $_GET['qtde_paginas_ini'], false);
    $where .= getRangeSql('valor', \Core\Util\Functions::realToDecimal($_GET['valor_ini']), \Core\Util\Functions::realToDecimal($_GET['valor_fim']), false);
    $busca_result = Functions::getStringBusca(trim($_GET['conteudo_anexo']));
    $busca_string = $busca_result['busca_string'];
    $numero = $busca_result['numero'];
    $id = $busca_result['id'];
    $ano = $busca_result['ano'];
    $ano_filter = !empty($_GET['exercicio']) ? $_GET['exercicio'] : (!empty($ano) ? $ano : null);
    $numero_filter = !empty($_GET['numero']) ? $_GET['numero'] : (!empty($numero) ? $numero : null);
    $id_filter = !empty($_GET['id']) ? $_GET['id'] : (!empty($id) ? $id : null);
    if (!empty($numero_filter)) {
        $where .= " AND numero=" . $numero_filter;
    }
    if (!empty($id_filter)) {
        $where .= " AND id=" . $id_filter;
    }
    if (!empty($ano_filter)) {
        $where .= " AND (exercicio=" . $ano_filter . " OR YEAR(data)=" . $ano_filter . ")";
    }
    if (!empty($busca_string)) {
        //$busca_string = Functions::shadowString($busca_string);
        $where .= " AND  MATCH (descricao,texto_ocr) AGAINST ('$busca_string' IN BOOLEAN MODE)";
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
