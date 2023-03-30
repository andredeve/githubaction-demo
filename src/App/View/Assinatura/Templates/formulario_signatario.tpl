<form id="form_add_signatario" class="form-horizontal" method="POST" action="{$app_url}assinatura/adicionarSignatario" style="display: none" >
    <input type="hidden" name="anexo_id" value="{$anexo->getId()}" />
    <input type="hidden" name="processo_id" value="{$anexo->getProcesso()->getId()}" />
    <input type="hidden" name="assinatura_id" value="{$assinatura->getId()}" />
    <div class="form-group">
        <label for="documento_signatario" class="col-form-label required">Signatário:</label>
        <select name="documento_signatario" id="documento_signatario" class="select2 form-control documento_signatario" required>
            <option value="">Selecione</option>
            {foreach $signatarios as $signatario}
                <option value="{$signatario->id}">{$signatario->nome}-{$signatario->id}</option>
            {/foreach}
        </select>
    </div>
    <hr/>
    <div class="form-group">
        <button
            type="submit"
            data-style="expand-right"
            class="btn btn-primary ladda-button"
        >
            <i class="fa fa-save"></i> Salvar
        </button>
        <button
            id="btn_add_signatario_cancelar"
            name="btn_add_signatario_cancelar"
            class="btn btn-light border small"
            type="button"
        >
            <i class="fa fa-times"></i>Cancelar
        </button>
    </div>
</form>
<script type="text/javascript" src="{$app_url}assets/js/view/assinatura/formulario_signatario.js?v={$file_version}"></script>