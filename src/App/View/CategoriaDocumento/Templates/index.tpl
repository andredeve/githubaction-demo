<a title="Cadastre um nova Categoria de Documento" class="btn btn-primary btn-loading"
   href="{$app_url}categoriaDocumento/cadastrar"><i class="fa fa-plus"></i> Nova</a>
<hr/>
<table id="tabelaCategorias" class="table table-bordered table-hover datatable table-sm">
    <thead class="bg-light">
    <tr>
        <th>Cód.</th>
        <th>Descrição</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    {foreach $categorias as $categoria}
        <tr>
            <td>{$categoria->getId()}</td>
            <td>{$categoria->getDescricao()}</td>
            <td>
                <a class="btn btn-info btn-xs btn-loading" title="Editar"
                   href="{$app_url}categoriaDocumento/editar/id/{$categoria->getId()}"><i class="fa fa-edit"></i></a>
                <a class="btn btn-danger btn-xs btn-excluir" title="Excluir"
                   href="{$app_url}categoriaDocumento/excluir/id/{$categoria->getId()}"><i class="fa fa-times"></i></a>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
