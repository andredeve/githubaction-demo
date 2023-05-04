<form id="formUsuario" method="POST" class="form-validate-no-ignore-hidden" action="{$app_url}usuario/{$acao}">
    <input type="hidden" name="entidade" value="usuario"/>
    <input id="usuario_id" type="hidden" name="id" value="{$usuario->getId()}"/>
<input id="pessoa_id" type="hidden" name="pessoa_id" value="{if !empty($usuario->getPessoa())}{$usuario->getPessoa()->getId()}{/if}"/>
    <input id="setor_id" type="hidden" name="setores_id" value="{implode(',',$usuario->getSetoresIds())}"/>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#geralTab" role="tab" aria-controls="geralTab">
                        <i class="fa fa-info"></i>
                        Geral
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#permissoesTab" role="tab" aria-controls="permissoesTab">
                        <i class="fa fa-lock"></i>
                        Permissões              
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="geralTab" role="tabpanel">
                    <div class="form-group">
                        <label class="col-form-label required">Nome:</label>
                        <input type="text" name="nome" value="{if !empty($usuario->getPessoa())}{$usuario->getPessoa()->getNome()}{/if}" autofocus="true" required="true"
                               class="form-control form-control-sm">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Cargo:</label>
                        <input type="text" name="cargo" value="{$usuario->getCargo()}" class="form-control form-control-sm">
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">E-mail:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class='fa fa-envelope-o'></i>
                                    </div>
                                </div>
                                <input id="email" type="email" name="email" value="{if !empty($usuario->getPessoa())}{$usuario->getPessoa()->getEmail()}{/if}"
                                       class="form-control form-control-sm" required="true">
                            </div>
                        </div>
                        {if $usuario->getId() eq ""}
                            <div class="col">
                                <label class="col-form-label required">Confirma e-mail:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class='fa fa-envelope-o'></i>
                                        </div>
                                    </div>
                                    <input type="email" equalTo="#email" name="email2" value="{if !empty($usuario->getPessoa())}{$usuario->getPessoa()->getEmail()}{/if}"
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
                                   name="telefone" value="{if !empty($usuario->getPessoa())}{$usuario->getPessoa()->getTelefone()}{/if}"
                                   class="form-control form-control-sm phone_with_ddd"
                            >
                        </div>
                        <div class="col">
                            <label class="col-form-label">Celular:</label>
                            <input type="text"
                                   autocomplete="false"
                                   name="celular"
                                   value="{if !empty($usuario->getPessoa())}{$usuario->getPessoa()->getCelular()}{/if}"
                                   class="form-control form-control-sm telefone"
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">Login:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                                <input type="text" name="login" minlength="5" value="{$usuario->getLogin()}"
                                       required="true" class="form-control form-control-sm minuscula">
                            </div>
                        </div>
                    </div>
                   
                </div>
                <div class="tab-pane" id="permissoesTab" role="tabpanel">
                    <div class="form-group">
                        <label class="col-form-label">Pasta Digitalização:</label>
                        <input type="text" name="nomePastaDigitalizacao" value="{$usuario->getNomePastaDigitalizacao()}"
                               class="form-control form-control-sm"/>
                        <small class="form-text text-muted">Se informado, o usuário poderá realizar digitalizações no
                            sistema. Para isso, basta configurar o driver da impressora para enviar os arquivos
                            digitalizados em "PDF" para o servidor em FTP no seguinte caminho caminho:
                            /var/www/html/LxProcessos/_files/processos/temp/_digitalizacao/[NOME_PASTA].
                        </small>
                    </div>
                    {include file="../../Usuario/Templates/permissoes.tpl"}
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col">
            <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
            {if $usuario->getId() neq ""}
                <a class="btn btn-danger btn-excluir" title="Excluir"
                   href="{$app_url}usuario/excluir/id/{$usuario->getId()}"><i class="fa fa-user-times"></i> Excluir</a>
                <a 
                    class="btn btn-warning border btn-enviar-senha" 
                    usuario="{$usuario->getId()}"
                    title="O sistema vai criar uma nova senha que será enviada para o e-mail cadastrado"
                    href="javascript:;"><i class="fa  fa-key"></i> Enviar Senha</a>
            {/if}
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

<script defer="true" src="{$app_url}assets/js/view/usuario/recuperar_senha.js"></script>