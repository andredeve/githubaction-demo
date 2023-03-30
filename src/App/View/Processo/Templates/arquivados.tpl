{include file="../../Processo/Templates/nav.tpl"}
<div class="processo-table">
    {include file="../../Processo/Templates/filtros.tpl"}
    <div class="pull-right">
        <button type="button" title="Atualizar lista de processos"
                class="btn btn-info text-white btn-atualizar-tabela"><i class="fa fa-refresh"></i> Atualizar
        </button>
        {*<button type="submit" title="Desarquivar processos(s) selecionado(s)"
                class="btn btn-info text-white ladda-button"><i class="fa fa-folder-open-o"></i> Desarquivar
        </button>*}
    </div>
    <table id="tabelaProcessosArquivados"
           class="table table-bordered table-hover table-sm tabelaProcessos text-center"
           hide_checkbox=""
           tipo_listagem="arquivados"
           verificar_vencimento="false"
           url="{$app_url}src/App/Ajax/Processo/listar_server_side.php?tipo_listagem=arquivados">
        <thead class="bg-light">
        <tr>
            <th></th>
            <th>
                <input type="checkbox" class="marcaTodosTabela" value="1"/>
            </th>
            <th>{$parametros['nomenclatura']}</th>
            <th>Assunto</th>
            <th>Interessado</th>
            <th>Setor Origem</th>
            <th>Setor Atual</th>
            <th class="text-center">Dt. Abertura</th>
            <th class="text-center">Dt. Arquivamento</th>
            <th class="text-center">Status</th>
            <th data-toggle="tooltip" data-placement="top" title="Verificação de assinatura no processo">Ass.?</th>
            <th></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
