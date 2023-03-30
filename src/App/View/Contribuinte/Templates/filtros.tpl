<table class="table table-sm table-bordered bg-light-default" style="margin-bottom: 5px;">
    <tr class="bg-light">
        {if isset($filtro_remessa) and $filtro_remessa eq true}
            <td><i class="fa fa-file-text-o"></i> Nº de Remessa</td>
        {/if}
        <td><i class="fa fa-file-text-o"></i> Nº de {$parametros['nomenclatura']}</td>
        <td><i class="fa fa-comment-o"></i> Assunto</td>{*CRIAR UMA LISTAGEM DOS ASSUNTOS EXISTENTES PARA O INTERESSADO NÃO PRECISAR DIGITAR, APENAS BUSCAR/SELECIONAR*}
        <td><i class="fa fa-calendar"></i> Data Abertura</td>
        <td>Status</td>
        <td></td>
    </tr>
    <tr>
        {if isset($filtro_remessa) and $filtro_remessa eq true}
            <td class="text-left"><input type="text" class="form-control form-control-sm numero_remessa"></td>
        {/if}
        <td class="text-left">
            <input type="text" class="form-control form-control-sm numero_processo">
        </td>
        <td class="text-left">
            <input type="text" class="form-control form-control-sm assunto_processo">
        </td>
        <td class="text-left">
            <input type="text" class="form-control form-control-sm data_abertura_processo datepicker data"/>
        </td>
        <td>
            <select class="form-control form-control-sm status_processo">
                <option value="">Todos</option>
                {foreach $status_processo as $status}
                    <option value="{$status->getDescricao()}">{$status->getDescricao()}</option>
                {/foreach}
            </select>
        </td>
        <td class="text-center vertical-middle">
            <a title="Limpar filtros" class="btn-limpar-filtros btn btn-xs btn-warning" href="#">
                <i class="fa fa-filter"></i>
            </a>
        </td>
    </tr>
</table>
