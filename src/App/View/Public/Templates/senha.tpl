<div id="alteraSenhaModal" class="modal">
    <form id="recuperaSenhaForm" role="form" action="{$app_url}src/App/Ajax/Usuario/recuperar_senha.php">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Esqueceu sua senha?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Digite seu login:</label>
                        <div class="input-group">
                            <input class="form-control" placeholder="Login" name="login" type="text" required="true">
                        </div>
                        <small class="form-text text-muted">
                            Nós lhe enviaremos instruções para redefinir sua senha.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success ladda-button" data-style="expand-right">Recuperar Senha</button>
                </div>
            </div>
        </div>
    </form>
</div>
