<a class="btn btn-primary btn-loading" href="{$app_url}assunto/cadastrar"><i class="fa fa-plus"></i> Novo</a>
<a target="_blank" class="btn btn-warning" href="{$app_url}assunto/imprimir"><i class="fa fa-print"></i> Imprimir</a>
<hr/>
<table class="table table-bordered table-hover datatable table-sm">
    <thead class="thead-light">
        <tr>
            <th>Cód.#</th>
            <th>Nome</th>
            <th>Prazo</th>
            <th>Sub-assunto de</th>
            <th class="text-center">Ativo?</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach $assuntos as $assunto}
            <tr>
                <td>{$assunto->getId()}</td>
                <td class="text-left">{$assunto->getDescricao()}</td>
                <td class="text-left">{$assunto->getPrazo()} dia(s) {if $assunto->getIsPrazoDiaUtil() eq true}úteis{else}corridos{/if}</td>
                <td>{$assunto->getAssuntoPai()->getDescricao()}</td>
                <td class="text-center">
                    {if $assunto->getIsAtivo() eq 1}
                        <label class="badge badge-success">SIM</label>
                    {else}
                        <label class="badge badge-danger">NÃO</label>
                    {/if}
                </td>
                <td>
                    <a class="btn btn-info btn-xs btn-loading" title="Editar" href="{$app_url}assunto/editar/id/{$assunto->getId()}"><i class="fa fa-edit"></i></a>
                    {if $assunto->getIsAtivo() eq 1}
                        <a class="btn btn-danger btn-xs btn-desativar" title="Desativar" href="{$app_url}assunto/desativar/id/{$assunto->getId()}"><i class="fa fa-times"></i></a>
                    {else}
                        <a class="btn btn-success btn-xs btn-loading btn-ajax" title="Reativar" href="{$app_url}assunto/reativar/id/{$assunto->getId()}"><i class="fa fa-undo"></i></a>
                    {/if}
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
