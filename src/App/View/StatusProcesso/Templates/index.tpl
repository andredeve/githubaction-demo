<a class="btn btn-primary btn-loading" href="{$app_url}statusProcesso/cadastrar"><i class="fa fa-plus"></i> Novo</a>
<a class="btn btn-warning disabled" href="{$app_url}statusProcesso/imprimir"><i class="fa fa-print"></i> Imprimir</a>
<hr/>
<table class="table table-bordered datatable table-sm">
    <thead class="thead-light">
        <tr>
            <th>Cód.#</th>
            <th>Descrição</th>
            <th class="text-center">Cor</th>
            <th class="text-center">Ativo</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach $status_processo as $status}
            <tr>
                <td>{$status->getId()}</td>
                <td>{$status->getDescricao()}</td>
                <td class="text-center">
                    <div style="background-color:{$status->getCor()};padding: 10px;"></div>
                </td>
                <td class="text-center">
                    {if $status->getAtivo() eq true}
                        Sim
                    {else}
                        Não
                    {/if}
                </td>
                {if $status->getAtivo() eq true}
                    <td>
                        <a class="btn btn-info btn-xs btn-loading" title="Editar" href="{$app_url}statusProcesso/editar/id/{$status->getId()}"><i class="fa fa-edit"></i></a>
                        <a class="btn btn-danger btn-xs btn-desativar {if $status->getId() lt 4}disabled{/if}" title="Desativar" href="{$app_url}statusProcesso/desativar/id/{$status->getId()}"><i class="fa fa-times"></i></a>
                    </td>
                {else}
                    <td>
                        <a class="btn btn-success btn-xs btn-loading btn-ajax" title="Reativar" href="{$app_url}statusProcesso/reativar/id/{$status->getId()}"><i class="fa fa-undo"></i></a>
                    </td>
                {/if}
            </tr>
        {/foreach}
    </tbody>
</table>
