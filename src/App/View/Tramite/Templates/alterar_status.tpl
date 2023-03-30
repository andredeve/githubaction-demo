<form id="alterarStatusForm" method="POST" class="form-horizontal" action="{$app_url}tramite/alterarStatus">
    <input type="hidden" name="tramite_id" value="{$tramite->getId()}"/>
    <table class="table table-bordered table-sm">
        <thead class="thead-light">
        <tr>
            <th>Setor Atual</th>
            <th>Responsável</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{$tramite->getSetorAtual()}</td>
            <td>{$tramite->getResponsavel()}</td>
        </tr>
        </tbody>
    </table>
    <div class="form-group">
        <label>Status atual:</label>
        <select id="select_status_processo" name="status_id" class="form-control form-control-sm">
            {foreach $status_processo as $status}
                <option is_arquivamento="{$status->getIsArquivamento()}" value="{$status->getId()}"
                        {if $tramite->getStatus()->getId() eq $status->getId()}selected{/if}>{$status->getDescricao()}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label>Parecer/Observações:</label>
        <textarea name="parecer" class="form-control" rows="6">{$tramite->getParecer()}</textarea>
        <small class="form-text text-muted">Altere ou complemente as observações do parecer atual.</small>
    </div>
    <div id="divLocalizacaoFisica" style="display: none" class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="fa fa-folder-open-o"></i> Localização Física</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            {$processo=$tramite->getProcesso()}
            {include file="../../Processo/Templates/localizacao.tpl"}
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i>
            Salvar
        </button>
        <a href="#" class="btn btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                    class="fa fa-times"></i> Cancelar</a>
    </div>
</form>
