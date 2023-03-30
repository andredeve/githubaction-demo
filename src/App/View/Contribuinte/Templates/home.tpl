<h5>Seja bem-vindo(a) {ucwords(strtolower($interessado_logado->getNome()))},</h5>
<p style="font-size: 1.3em">Aqui neste portal você poderá fazer e acompanhar suas solicitações junto aos órgãos competentes.
<br/>Fique atento ao seu e-mail, pois é por ele que te avisaremos sobre toda movimentação de sua solicitação de abertura de protocolo.</p>
{include file="../../Contribuinte/Templates/nav.tpl"}
<div class="processo-table">
    {include file="../../Contribuinte/Templates/filtros.tpl"}
    <table id="tabelaProcessosContribuintes"
        class="table table-bordered table-hover table-sm tabelaProcessosContribuintes text-center"
        hide_checkbox=""
        verificar_vencimento="false"
        processoExterno="true"
        url="{$app_url}src/App/Ajax/Contribuinte/listar_server_side.php">
        <thead class="bg-light">
        <tr>
            <th></th>
            <th>{$parametros['nomenclatura']}</th>
            <th>Assunto</th>
            <th class="text-center">Data Abertura</th>
            <th class="text-center">Status</th>
            <th></th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>