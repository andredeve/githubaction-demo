{include file="../../Processo/Templates/nav.tpl"}
<div class="processo-table">
    {include file="../../Processo/Templates/filtros.tpl"}
    <form id="processosEmAbertoForm" method="POST" action="">
        <div class="pull-right">
            <button type="button" title="Atualizar lista de processos"
                    class="btn btn-info text-white btn-atualizar-tabela"><i class="fa fa-refresh"></i> Atualizar
            </button>
            <button onclick="$(this).closest('form').attr('action','{$app_url}src/App/View/Tramite/devolver.php');"
            type="submit" modal_id="devolverProcessoModal"
            title="Recusar processos(s) selecionado(s)" class="btn btn-danger text-white">
                <i class="fa fa-times"></i> Recusar
            </button>
            <button onclick="$(this).closest('form').attr('action','{$app_url}src/App/View/Tramite/devolver_origem.php');"
            type="submit" modal_id="devolverProcessoModal"
            title="Devolver processos(s) selecionado(s)" class="btn btn-secondary text-white">
                <i class="fa fa-reply"></i> Devolver à origem
            </button>
            <button onclick="$(this).closest('form').attr('action','{$app_url}src/App/View/Tramite/tramitar.php');"
                    type="submit" modal_id="tramitarProcessoModal"
                    title="Tramitar processos(s) selecionado(s)" class="btn btn-success text-white">
                <i class="fa fa-send-o"></i> Tramitar
            </button>
            <button type="submit" modal_id="arquivarProcessoModal"
                    onclick="$(this).closest('form').attr('action','{$app_url}src/App/View/Processo/arquivar_massa.php');"
                    title="Arquivar processos(s) selecionado(s)"
                    class="btn btn-info text-white"><i class="fa fa-folder-open-o"></i> Arquivar
            </button>
        </div>
        <table id="tabelaProcessosTramitar"
               class="table table-bordered table-hover table-sm tabelaProcessos text-center"
               hide_checkbox=""
               tipo_listagem="abertos"
               verificar_vencimento="true"
               url="{$app_url}src/App/Ajax/Processo/listar_server_side.php?tipo_listagem=abertos">
            <thead class="bg-light">
            <tr>
                <th></th>
                <th><input type="checkbox" class="marcaTodosTabela" name="marcar_todos" value="1"/></th>
                <th>{$parametros['nomenclatura']}</th>
                <th>Assunto</th>
                <th>Interessado</th>
                <th>Setor Origem</th>
                <th>Setor Atual</th>
                <th class="text-center">Data Trâmite</th>
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
