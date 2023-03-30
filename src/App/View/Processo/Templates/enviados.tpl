{include file="../../Processo/Templates/nav.tpl"}
<div class="alert alert-warning">
    <i class="fa fa-info-circle"></i> <strong>Aviso:</strong> o cancelamento de envio não é aplicável a
    protocolos que já foram recebidos ou que possuam um fluxograma já definido.
</div>
<div class="processo-table">
    {include file="../../Processo/Templates/filtros.tpl"}
    <div class="pull-right">
        <button type="button" title="Atualizar lista de processos"
                class="btn btn-info text-white btn-atualizar-tabela"><i class="fa fa-refresh"></i> Atualizar
        </button>
{*        <a title="Cancelar trâmite(s) selecionado(s)" class="btn btn-danger text-white"><i*}
{*                    class="fa fa-times"></i> Cancelar</a>*}
    </div>
    <table id="tabelaProcessosEnviados"
           class="table table-bordered table-hover table-sm tabelaProcessos text-center"
           hide_checkbox="hidden"
           tipo_listagem="enviados"
           verificar_vencimento="true"
           url="{$app_url}src/App/Ajax/Processo/listar_server_side.php?tipo_listagem=enviados">
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
            <th class="text-center">Envio</th>
            <th class="text-center">Venc. Trâmite</th>
            <th class="text-center">Venc. {$nomenclatura}</th>
            <th class="text-center">Status</th>
            <th class="text-center">Recebido?</th>
            <th data-toggle="tooltip" data-placement="top" title="Verificação de assinatura no processo">Ass.?</th>
            <th></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

