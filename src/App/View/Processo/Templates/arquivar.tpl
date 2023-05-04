{if !isset($modal)}
    <form id="arquivarProcessoForm" method="POST" class="form-horizontal" action="{$app_url}processo/arquivar">
{/if}
    <input type="hidden" name="processo_id" value="{$processo->getId()}"/>
    <div class="form-group">
        <label>Assunto:</label>
        <div class="form-control-static text-muted">{$processo->getAssunto()}</div>
    </div>
    <div class="form-group">
        <label class="required">Motivo do arquivamento:</label>
        <textarea name="justificativa" class="form-control" rows="3" required="true"></textarea>
        <small class="form-text text-muted">Descreva o porquê que esse processo está sendo arquivado neste momento.
        </small>
    </div>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="fa fa-folder-open-o"></i> Localização Física</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            {include file="../../Processo/Templates/localizacao.tpl"}
        </div>
    </div>
    {if !isset($modal)}
        <hr/>
        <div class="form-group">
            <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i
                        class="fa fa-archive"></i> Arquivar
            </button>
            <a href="#" class="btn btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                        class="fa fa-times"></i> Cancelar</a>
        </div>
</form>
{/if}
