<div id="anexoModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Anexar Arquivo do Computador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="file-loading">
                        <input id="arquivo_processo" name="arquivo_processo" type="file" data-show-caption="true" data-msg-placeholder="Selecione um arquivo para anexar...">
                    </div>
                    <small class="form-text text-muted">* Tamanho máximo arquivo: {ini_get('post_max_size')}</small>
                    <small class="form-text text-muted">* Extensões permitidas: pdf, jpg, jpeg, gif, png, xsl, xslx, mp3 e mp4.</small>
                    <div id="kartik-file-errors"></div>
                </div>
            </div>
        </div>
    </div>
</div>
