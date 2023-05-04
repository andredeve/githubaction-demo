<form id="alteraSenhaForm" method="POST" class="form-horizontal" action="{$app_url}Usuario/atualizarSenha">
    <input type="hidden" name="entidade" value="usuario"/>
    <input type="hidden" name="id" value="{$usuario->getId()}"/>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Senha Atual:</label>
        <div class="col-lg-4 col-md-5">
            <div class="input-group">
                <div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-key"></i></div></div>
                <input type="password" id="senhaAtual" name="senha_atual" class="form-control" autofocus="true" required="true">
            </div>
            <span id="msgSenhaAtual" class="form-text text-danger hidden"></span>
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Nova Senha:</label>
        <div class="col-lg-4 col-md-5">
            <div class="input-group">
                <div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-key"></i></div></div>
                <input type="password" 
                    id="novaSenha" 
                    name="senha" 
                    class="form-control" 
                    required="true">
            </div>
            <strong>A senha deve seguir as regras:</strong>
            <ul>
                <li id="letter" class="text-danger"><i id="iLetter" class="fa fa-times"></i> <strong>Uma letra minúscula</strong></li>
                <li id="capital" class="text-danger"><i id="iCapital"  class="fa fa-times"></i> <strong>Uma letra maiúscula</strong></li>
                <li id="number" class="text-danger"><i id="iNumber"  class="fa fa-times"></i> <strong>Um número</strong></li>
                <li id="length" class="text-danger"><i id="iLength"  class="fa fa-times"></i> <strong>No mínimo characters 8</strong></li>
            </ul>
        </div>
        <style>
            li {
                list-style-type: none;
            }
        </style>
        <div class="col-lg-2 col-md-2">
            <div class="progress" style="display: none">
                <div id="passwordMeter" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">

                </div>
            </div>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Confirma:</label>
        <div class="col-lg-4 col-md-5">
            <div class="input-group">
                <div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-key"></i></div></div>
                <input type="password" id="confirmaSenha" name="confirmaSenha" class="form-control" required="true">        
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="ml-auto col-md-10">
            <button type="submit" class="btn btn-primary ladda-button" data-style="zoom-in"> <i class="fa fa-save"></i> Salvar</button>
            <a class="btn btn-light border btn-loading" href="{$app_url}"><i class="fa fa-times"></i> Cancelar</a>
        </div>
    </div>
</form>

<script defer="true" src="{$app_url}assets/js/view/usuario/validar_senha.js?v=167rr5"></script>