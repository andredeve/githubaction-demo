<a class="btn btn-primary btn-loading" href="{$app_url}setor/cadastrar"><i class="fa fa-plus"></i> Novo</a>
<a target="_blank" class="btn btn-warning" href="{$app_url}setor/imprimir"><i class="fa fa-print"></i> Imprimir</a>
<hr/>
<table id="tabelaSetores" class="table table-bordered table-hover datatable table-sm">
    <thead class="thead-light">
    <tr>
        <th>Cód.#</th>
        <th>Órgão:</th>
        <th>Unidade</th>
        <th>Nome</th>
        <th>Sigla</th>
        <th>Sub-setor de</th>
        <th class="text-center">Ativo?</th>
        <th class="text-center">Usuários</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    {foreach $setores as $setor}
        <tr>
            <td>{$setor->getId()}</td>
            <td>{$setor->getOrgao()}</td>
            <td>{$setor->getUnidade()}</td>
            <td class="text-left">{$setor->getNome()}</td>
            <td>{$setor->getSigla()}</td>
            <td>{$setor->getSetorPai()}</td>
            <td class="text-center">
                {if $setor->getIsAtivo() eq 1}
                    <label class="badge badge-success">SIM</label>
                {else}
                    <label class="badge badge-danger">NÃO</label>
                {/if}
            </td>
            <td class="text-center">
                <button type="button" setor="{$setor->getNome()}" setor_id="{$setor->getId()}"
                        class="btn btn-xs btn-link btn-ver-usuarios-setor"><i
                            class="fa fa-user"></i> {count($setor->getUsuarios())}
                </button>
            </td>
            <td>
                <a class="btn btn-info btn-xs btn-loading" title="Editar" href="{$app_url}setor/editar/id/{$setor->getId()}">
                    <i class="fa fa-edit"></i>
                </a>
                {if $setor->getIsAtivo() eq 1}
                    <a class="btn btn-danger btn-xs btn-desativar" title="Desativar" href="{$app_url}setor/desativar/id/{$setor->getId()}">
                        <i class="fa fa-times"></i>
                    </a>
                {else}
                    <a class="btn btn-success btn-xs btn-loading btn-ajax" title="Reativar" href="{$app_url}setor/reativar/id/{$setor->getId()}">
                        <i class="fa fa-undo"></i>
                    </a>
                {/if}
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
