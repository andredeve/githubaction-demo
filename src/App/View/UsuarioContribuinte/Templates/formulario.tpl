<form id="formUsuario" method="POST" class="form-validate-no-ignore-hidden" action="{$app_url}usuarioContribuinte/{$acao}">
    <input type="hidden" name="entidade" value="usuario"/>
    <input id="usuario_id" type="hidden" name="id" value="{$usuario->getId()}"/>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#geralTab" role="tab" aria-controls="geralTab"><i
                                class="fa fa-info"></i> Geral</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="geralTab" role="tabpanel">
                    <div class="form-group">
                        <label class="col-form-label required">Nome:</label>
                        <input type="text" name="nome" value="{if isset($usuario->getPessoa())}{$usuario->getPessoa()->getNome()}{else}Não encontrado{/if}" autofocus="true" required="true"
                               class="form-control form-control-sm">
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">Cargo:</label>
                            <input type="text" name="cargo" value="{$usuario->getCargo()}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 col-xs-12">
                            <label class="col-form-label">Acesso ativo?</label><br/>
                            <div class="form-check form-check-inline">
                                <div class="custom-control custom-radio">
                                    <input id="radioAtivo1" type="radio" name="ativo" value="1" class="custom-control-input"
                                        {if $usuario->getAtivo() eq true}checked{/if}>
                                    <label class="custom-control-label" for="radioAtivo1">Sim</label>
                                </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <div class="custom-control custom-radio">
                                    <input id="radioAtivo2" type="radio" name="ativo" value="0" class="custom-control-input"
                                        {if $usuario->getAtivo() eq false}checked{/if}>
                                    <label class="custom-control-label" for="radioAtivo2">Não</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">E-mail:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class='fa fa-envelope-o'></i></div>
                                </div>
                                <input id="email" type="email" name="email" value="{if isset($usuario->getPessoa())}{$usuario->getPessoa()->getEmail()}{else}Não encontrado{/if}"
                                       class="form-control form-control-sm" required="true">
                            </div>
                        </div>
                        {if $usuario->getId() eq ""}
                            <div class="col">
                                <label class="col-form-label required">Confirma e-mail:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class='fa fa-envelope-o'></i></div>
                                    </div>
                                    <input type="email" equalTo="#email" name="email2" value="{if isset($usuario->getPessoa())}{$usuario->getPessoa()->getEmail()}{else}Não encontrado{/if}"
                                           class="form-control form-control-sm" required="true">
                                </div>
                            </div>
                        {/if}
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">Telefone:</label>
                            <input type="text"
                                   autocomplete="false"
                                   name="telefone" value="{$usuario->getPessoa()->getTelefone()}"
                                   class="form-control form-control-sm phone_with_ddd"
                            >
                        </div>
                        <div class="col">
                            <label class="col-form-label">Celular:</label>
                            <input type="text"
                                   autocomplete="false"
                                   name="celular"
                                   value="{$usuario->getPessoa()->getCelular()}"
                                   class="form-control form-control-sm telefone"
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">Login:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fa fa-user"></i></div>
                                </div>
                                <input type="text" name="login" minlength="5" value="{$usuario->getLogin()}"
                                       required="true" class="form-control form-control-sm minuscula">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label {if $acao eq 'inserir'}required{/if}">Senha:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fa fa-key"></i></div>
                                </div>
                                <input type="password" id="senha" minlength="5" name="senha"
                                       class="form-control form-control-sm" {if $acao eq 'inserir'}required="true"{/if}>
                            </div>
                            <div id="messages"></div>
                        </div>
                        <div class="col">
                            <label class="col-form-label {if $acao eq 'inserir'}required{/if}">Confirma senha:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fa fa-key"></i></div>
                                </div>
                                <input type="password" id="confirmaSenha" equalTo="#senha" name="confirmaSenha"
                                       placeholder="Confirme a senha" class="form-control form-control-sm"
                                       {if $acao eq 'inserir'}required="true"{/if}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col">
            <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
            <a class="btn btn-light border btn-loading" href="{$app_url}usuario"><i class="fa fa-times"></i> Cancelar</a>
        </div>
        <div class="col-md-4 mr-auto text-right">
            {if $usuario->getId() neq ""}
                <p class="form-control-static text-muted">
                    Data cadastro registro: {$usuario->getDataCadastro()->format('d/m/Y')}<br/>
                    Última alteração:
                    {if $usuario->getUltimaAlteracao() neq ""}
                        {$usuario->getUltimaAlteracao()->format('d/m/Y')} às {$usuario->getUltimaAlteracao()->format('H:i')}
                    {else}
                        Não registrado
                    {/if}
                </p>
            {/if}
        </div>
    </div>
</form>

