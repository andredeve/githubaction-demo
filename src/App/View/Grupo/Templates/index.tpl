<a class="btn btn-primary btn-loading" href="{$app_url}grupo/cadastrar"><i class="fa fa-plus"></i> Novo</a>
<a class="btn btn-success btn-loading" href="{$app_url}usuario"><i class="fa fa-users"></i> Usuários</a>
<a class="btn btn-warning disabled" href="{$app_url}grupo/imprimir"><i class="fa fa-print"></i> Imprimir</a>
<hr/>
<table id="tabelaGrupos" class="table table-bordered table-hover datatable table-sm">
    <thead class="thead-light">
        <tr>
            <th>Cód.#</th>
            <th>Nome</th>
            <th class="text-center">Usuários</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach $grupos as $grupo}
            <tr>
                <td>{$grupo->getId()}</td>
                <td class="text-left">{$grupo->getNome()}</td>
                <td class="text-center">
                    <a grupo="{$grupo->getNome()}" grupo_id="{$grupo->getId()}" class="btn-ver-usuarios-grupo" href="#"><i class="fa fa-user"></i> {count($grupo->getUsuarios())}</a>
                </td>
                <td>
                    <a class="btn btn-info btn-xs btn-loading" title="Editar" href="{$app_url}grupo/editar/id/{$grupo->getId()}"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-danger btn-xs btn-excluir" title="Excluir" href="{$app_url}grupo/excluir/id/{$grupo->getId()}"><i class="fa fa-times"></i></a>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
