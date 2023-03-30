{include file="../../Processo/Templates/nav.tpl"}
<div class="processo-table">
    {include file="../../Processo/Templates/filtros.tpl"}
    <form id="contribuintesProcessosForm" method="POST" action="{$app_url}tramite/receber">
        <div class="pull-right">
            <button type="button" title="Atualizar lista de processos" class="btn btn-info text-white btn-atualizar-tabela">
                <i class="fa fa-refresh"></i> Atualizar
            </button>
        </div>
        <table id="tabelaProcessosReceber"
               class="table table-bordered table-hover table-sm tabelaProcessos text-center"
               hide_checkbox="hidden"
               tipo_listagem="contribuintes"
               verificar_vencimento="true"
               url="{$app_url}src/App/Ajax/Processo/listar_server_side.php?tipo_listagem=contribuintes">
            <thead class="bg-light">
            <tr>
                <th></th>
                <th><input type="checkbox" class="marcaTodosTabela" name="marcar_todos" value="1"/></th>
                <th>{$parametros['nomenclatura']}</th>
                <th>Assunto</th>
                <th>Interessado</th>
                <th>Setor Origem</th>
                <th>Setor Destino</th>
                <th class="text-center">Data Cadastro</th>
                <th class="text-center">Venc. Trâmite</th>
                <th class="text-center">Venc. {$nomenclatura}</th>
                <th class="text-center">Status</th>
                <th data-toggle="tooltip" data-placement="top" title="Verificação de assinatura no processo">Ass.?</th>
                <th></th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </form>
</div>
