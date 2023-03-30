<form id="gerarRemessaForm" method="POST" action="{$app_url}remessa/inserir">
    <table style="font-size: 11px" id="resultadosPesquisa"
           class="table table-bordered table-sm table-hover tabelaListaProcessos">
        <thead class="bg-light">
        <tr>
            <th></th>
            <th class="text-center">Número Processo</th>
            <th class="text-center">Exercício</th>
            <th class="text-center">Data Envio</th>
            <th class="text-left">Assunto</th>
            <th class="text-left">Interessado</th>
            <th class="text-left">Setor Origem</th>
            <th class="text-left">Setor Destino</th>
        </tr>
        </thead>
        <tbody>
        {foreach $resultado as $tramite}
            {$processo=$tramite->getProcesso()}
            <tr id="visualizar:{$processo->getId()}" title="{$processo->getObjeto()}">
                <td class="text-center">
                    <input type="checkbox" name="tramite_id[]" value="{$tramite->getId()}" checked/>
                </td>
                <td class="text-center">{$processo->getNumero()}</td>
                <td class="text-center">{$processo->getExercicio()}</td>
                <td class="text-center">{$processo->getDataAbertura(true)}</td>
                <td>{$processo->getAssunto()}</td>
                <td>{$processo->getInteressado()}</td>
                <td>{$tramite->getSetorAnterior()}</td>
                <td>{$tramite->getSetorAtual()}</td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="8" class="text-muted">*Nenhum processo encontrado.</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {if count($resultado) gt 0}
        <hr/>
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i
                    class="fa fa-refresh"></i> Gerar
        </button>
        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                    class="fa fa-times"></i> Cancelar</a>
    {/if}
</form>