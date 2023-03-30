<form id="formUsuario" method="POST" class="form-horizontal form-validate" action="{$app_url}usuario/atualizarPerfil">
    <input type="hidden" name="id" value="{$usuario->getId()}"/>
    <div class="form-group row">
        <label class="col-md-2 col-form-label">Nome:</label>
        <div class="col-md-10 col-lg-8">
            <input type="text" name="nome" value="{$usuario->getPessoa()->getNome()}" autofocus="true" required="true" class="form-control">
        </div>
    </div>
    {if $usuario->getTipo() != App\Enum\TipoUsuario::INTERESSADO}
        <div class="form-group row">
            <label class="col-md-2 col-form-label">Cargo:</label>
            <div class="col-md-10 col-lg-8">
                <input type="text" name="cargo" value="{$usuario->getCargo()}" class="form-control">
            </div>
        </div>
    {/if}
    <div class="form-group row">
        <label class="col-md-2 col-form-label">E-mail:</label>
        <div class="col-md-10 col-lg-8">
            <div class="input-group">
                <div class="input-group-prepend"><div class="input-group-text"><i class='fa fa-envelope-o'></i></div></div>
                <input type="email" name="email" value="{$usuario->getPessoa()->getEmail()}" class="form-control" required="true">
            </div>
        </div>
    </div>
    {if $usuario->getTipo() != App\Enum\TipoUsuario::INTERESSADO}
        <div class="form-group row">
            <label class="col-md-2 col-form-label">Login:</label>
            <div class="col-md-10 col-lg-8">
                <div class="input-group">
                    <div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-user"></i></div></div>
                    <input type="text" name="login" value="{$usuario->getLogin()}" required="true" class="form-control minuscula" readonly="true">
                </div>
            </div>
        </div>
    {/if}
    {*<fieldset>
        <legend><i class='fa fa-desktop'></i> Layout</legend>
        <div class="form-group row">
            <label class="col-2 col-form-label">Tema:</label>
            <div class='col'>
                <select id='select_tema' name="tema" class='form-control select2'>
                    {foreach $temas as $tema}
                        <option value="{$tema['value']}" {if $parametros['tema'] eq $tema['value']}selected{/if}>{$tema['description']}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class='control-label col-2'>Tema barra navegação:</label>
            <div class='col'>
                <select id='select_tema_navbar' name="tema_navbar" class='form-control select2'>
                    <option value="default" {if $parametros['tema_navbar'] eq 'default'}selected{/if}>Padrão</option>
                    <option value="inverse" {if $parametros['tema_navbar'] eq 'inverse'}selected{/if}>Invertido</option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label class='control-label col-2'>Cor Faixa:</label>
            <div class='col-2'>
                <input id="cor_faixa" type="color" name="cor_faixa" value="{$parametros['cor_faixa']}" class='form-control'/>
            </div>
        </div>
    </fieldset>*}
    <hr/>
    <div class="form-group row">
        <div class="col-10 ml-auto">
            <button type="submit" class="btn btn-primary"> <i class="fa fa-save"></i> Salvar</button>
            <a class="btn btn-light border btn-loading" href="{$app_url}"><i class="fa fa-times"></i> Cancelar</a>
        </div>
    </div>
</form>