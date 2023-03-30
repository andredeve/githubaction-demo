<?php

use Core\Util\Inflector;

include '../../../../bootstrap.php';
include './init.php';

while ($arquivo = $diretorio->read()) {
    if (is_file($dir . 'Model/' . $arquivo)) {
        $aux = explode('.', $arquivo);
        createViewFiles($aux[0]);
    }
}

function createViewFiles($entity) {
    global $dir;
    $view_path = $dir . 'View/' . $entity . "/";
    if (!is_dir($view_path)) {
        mkdir($view_path);
        mkdir($view_path . "Templates/");
        createCadastrar($view_path, $entity);
        createEditar($view_path, $entity);
        createIndex($view_path, $entity);
        createFormulario($view_path, $entity);
        createIndexTpl($view_path, $entity);
    }
}

function createCadastrar($view_path, $entity) {
    $conteudo = "<?php"
            . "\nuse App\Model\\$entity;"
            . "\n\$smarty->assign('page_title', 'Cadastrar $entity');"
            . "\n\$smarty->assign('page_icon', 'fa fa-plus');"
            . "\n\$smarty->assign('acao', 'inserir');"
            . "\n\$smarty->assign('" . strtolower($entity) . "', (new $entity()));";
    file_put_contents($view_path . "cadastrar.php", $conteudo);
}

function createEditar($view_path, $entity) {
    $conteudo = "<?php"
            . "\n\$smarty->assign('page_title', 'Editar $entity');"
            . "\n\$smarty->assign('page_icon', 'fa fa-edit');"
            . "\n\$smarty->assign('acao', 'atualizar');"
            . "\n\$smarty->assign('" . strtolower($entity) . "', \$_REQUEST['objeto']);";
    file_put_contents($view_path . "editar.php", $conteudo);
}

function createIndex($view_path, $entity) {
    $conteudo = "<?php"
            . "\n\$smarty->assign('page_title', 'Lista de " . Inflector::pluralize($entity) . "');"
            . "\n\$smarty->assign('page_icon', 'fa fa-th-list');"
            . "\n\$smarty->assign('" . Inflector::pluralize(strtolower($entity)) . "', \$_REQUEST['registros']);";
    file_put_contents($view_path . "index.php", $conteudo);
}

function createFormulario($view_path, $entity) {
    $api = new ReflectionClass('App\Model\\' . $entity);
    $objeto = strtolower($entity);
    $campos = $api->getDefaultProperties();
    $conteudo = "<form id='formServico' method='POST' class='form-horizontal form-validate' action='{\$app_url}$objeto/{\$acao}'>"
            . "\n<input type='hidden' name='entidade' value='$objeto'/>"
            . "\n<input type='hidden' name='id' value='{\$" . $objeto . "->getId()}'/>";
    foreach ($campos as $campo => $res) {
        $conteudo .= "\n<div class='form-group'>"
                . "\n<label class='col-md-2 control-label'>" . ucfirst($campo) . ":</label>"
                . "\n<div class='col-md-10 col-lg-8'>"
                . "\n<input type='text' name='$campo' value='{\$" . $objeto . "->get" . ucfirst($campo) . "()}' class='form-control'/>"
                . "\n</div>\n</div>";
    }
    $conteudo .= "\n{if \$" . $objeto . "->getId() neq ''}"
            . "\n<div class='form-group'>"
            . "\n<div class='col-md-4 col-md-offset-2'>"
            . "\n<p class='form-control-static text-muted'>"
            . "\n   Data cadastro: {\$" . $objeto . "->getDataCadastro()->format('d/m/Y')}<br/>"
            . "\nÚltimo alteração:"
            . "\n{if \$" . $objeto . "->getUltimaAlteracao() neq ''}"
            . "\n{\$" . $objeto . "->getUltimaAlteracao()->format('d/m/Y H:i:s')}"
            . "\n{else}"
            . "\nNão registrado"
            . "\n{/if}"
            . " \n</p>"
            . "\n</div>"
            . "\n</div>"
            . "\n{/if}"
            . "\n<hr/>"
            . "\n<div class='form-group'>"
            . "\n<div class='col-md-offset-2 col-md-10'>"
            . "\n<button type='submit' class='btn btn-primary'> <i class='fa fa-save'></i> Salvar</button>"
            . "\n{if \$" . $objeto . "->getId() neq ''}"
            . "\n <a class='btn btn-danger btn-excluir' title='Excluir' href='{\$app_url}$objeto/excluir/id/{\$" . $objeto . "->getId()}'><i class='fa fa-times'></i> Excluir</a>"
            . "\n{/if}"
            . "\n<a class='btn btn-default btn-loading' href='{\$app_url}$objeto'><i class='fa fa-times'></i> Cancelar</a>"
            . "\n</div>"
            . "\n</div>"
            . "\n</form>";
    file_put_contents($view_path . "Templates/formulario.tpl", $conteudo);
}

function createIndexTpl($view_path, $entity) {
    $api = new ReflectionClass('App\Model\\' . $entity);
    $objeto = strtolower($entity);
    $campos = $api->getDefaultProperties();
    $conteudo = "\n<a class='btn btn-primary btn-loading' href='{\$app_url}$objeto/cadastrar'><i class='fa fa-plus'></i> Cadastrar</a>
                <hr/>
                <table class='table table-bordered  table-condensed table-striped table-hover text-center datatable'>
                <thead>
                <tr>";
    foreach ($campos as $campo => $res) {
        $conteudo .= "\n<th>" . ucfirst($campo) . "</th>";
    }
    $conteudo .= "<th class='col-lg-1 col-md-2'></th>
                </tr>
                </thead>
                <tbody>
                {foreach $" . Inflector::pluralize(strtolower($entity)) . " as $$objeto}";
    $conteudo .= "\n<tr>";
    foreach ($campos as $campo => $res) {
        $conteudo .= "\n<td>{\$" . $objeto . "->get" . ucfirst($campo) . "()}</td>";
    }
    $conteudo .= "\n<td class='col-lg-1 col-md-2'>
                                <a class='btn btn-info btn-xs btn-loading' title='Editar' href='{\$app_url}$objeto/editar/id/{\$" . $objeto . "->getId()}'><i class='fa fa-edit'></i></a>
                                <a class='btn btn-danger btn-xs btn-excluir' title='Excluir' href='{\$app_url}$objeto/excluir/id/{\$" . $objeto . "->getId()}'><i class='fa fa-times'></i></a>
                  </td>
                </tr>
                {/foreach}
                </tbody>
                </table>";
    file_put_contents($view_path . "Templates/index.tpl", $conteudo);
}
