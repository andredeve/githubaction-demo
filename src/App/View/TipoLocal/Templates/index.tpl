<a class="btn btn-primary btn-loading" href="{$app_url}tipoLocal/cadastrar"><i class="fa fa-plus"></i> Novo</a>
<hr/>
<table class="table table-bordered datatable table-sm">
    <thead class="thead-light">
    <tr>
        <th>Cód.#</th>
        <th>Descrição</th>
        <th>Data Cadastro</th>
        <th>Última alteração</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    {foreach $locais as $local}
        <tr>
            <td>{$local->getId()}</td>
            <td>{$local->getDescricao()}</td>
            <td>{$local->getDataCadastro()->format('d/m/Y')}</td>
            <td>{if $local->getUltimaAlteracao() neq null}{$local->getUltimaAlteracao()->format('d/m/Y - H:i:s')}{/if}</td>
            <td>
                <a class="btn btn-info btn-xs btn-loading" title="Editar"
                   href="{$app_url}tipoLocal/editar/id/{$local->getId()}"><i class="fa fa-edit"></i></a>
                <a class="btn btn-danger btn-xs btn-excluir" title="Excluir"
                   href="{$app_url}tipoLocal/excluir/id/{$local->getId()}"><i class="fa fa-times"></i></a>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
