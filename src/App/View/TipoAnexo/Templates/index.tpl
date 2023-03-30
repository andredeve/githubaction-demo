<a class="btn btn-primary btn-loading" href="{$app_url}tipoAnexo/cadastrar"><i class="fa fa-plus"></i> Novo</a>
<hr/>
<table class="table table-bordered datatable table-sm">
    <thead class="thead-light">
        <tr>
            <th>Cód.#</th>
            <th>Descrição</th>
            <th class="text-center">Ativo</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach $tipos_anexo as $tipoAnexo}
            <tr>
                <td>{$tipoAnexo->getId()}</td>
                <td>{$tipoAnexo->getDescricao()}</td>
                <td class="text-center">{if $tipoAnexo->getAtivo()} Sim {else} Não {/if}</td>
                <td>
                    <a class="btn btn-info btn-xs btn-loading" title="Editar" href="{$app_url}tipoAnexo/editar/id/{$tipoAnexo->getId()}"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-danger btn-xs btn-excluir" title="Excluir" href="{$app_url}tipoAnexo/excluir/id/{$tipoAnexo->getId()}"><i class="fa fa-times"></i></a>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
