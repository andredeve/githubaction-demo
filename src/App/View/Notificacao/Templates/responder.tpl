<form id="responderNotificacaoForm" method="POST" action="{$app_url}notificacao/responder">
    <input type="hidden" name="notificacao_id" value="{$notificacao->getId()}"/>
    <div class="card">
        <div class="card-body bg-light">
            {$notificacao->getTexto()}
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label font-weight-bold">Digite sua resposta para a notificação:</label>
        <textarea name="resposta" rows="4" class="form-control form-control-sm editor" required></textarea>
    </div>
    <hr/>
    <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-send-o"></i> Enviar resposta</button>
    <button type="button" class="btn btn-light border" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
</form>
