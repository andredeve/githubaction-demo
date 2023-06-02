<form id="grupoForm" method="POST" action="{$app_url}grupo/{$acao}" class="form-horizontal form-validate">
    <input type="hidden" name="id" value="{$grupo->getId()}"/>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Nome:</label>
            <input type="text" placeholder="Nome do grupo" autofocus="true" class="form-control form-control-sm" value="{$grupo->getNome()}" name="nome" required="true">
        </div>
    </div>
    <br/>
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#permissoesGrupoTab" role="tab" aria-controls="permissoesGrupoTab"><i class="fa fa-lock"></i> Permissões</a>
                </li>
                {if $grupo->getId() neq ""}
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#usuariosTab" role="tab" aria-controls="usuariosTab"><i class="fa fa-users"></i> Usuários ({count($grupo->getUsuarios())})</a>
                    </li>
                {/if}
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="permissoesGrupoTab" role="tabpanel">
                    <table id="tabelaAcoes" class="table table-bordered table-sm text-center">
                        <thead class="thead-light">
                            <tr>
                                <th></th>
                                <th>Gerar Relatórios</th>
                                <th>Tramitar Protocolos</th>
                                <th>Arquivar/Desarquivar Protocolos</th>
                                <th>Cadastrar Retroativos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="checkbox" name="marcar_todos" class="marcaTodosTabela" {if $grupo->getRelatorios() eq true and $grupo->getTramitar() eq true and $grupo->getArquivar() eq true}checked{/if}/>
                                </td>
                                <td>
                                    <input type="checkbox" name="relatorios" value="1" {if $grupo->getRelatorios() eq true}checked{/if}/>
                                </td>
                                <td>
                                    <input type="checkbox" name="tramitar" value="1" {if $grupo->getTramitar() eq true}checked{/if}/>
                                </td>
                                <td>
                                    <input type="checkbox" name="arquivar" value="1" {if $grupo->getArquivar() eq true}checked{/if}/>
                                </td>
                                <td>
                                    <input type="checkbox" name="retroativo" value="1" {if $grupo->getRetroativo() eq true}checked{/if}/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table id="tabelaPermissoes" class="table table-bordered table-sm text-center">
                        <thead class="thead-light">
                            <tr>
                                <th><input type="checkbox" name="marcar_todos" class="marcaTodosTabela"/></th>
                                <th>Entidade</th>
                                <th>Inserir</th>
                                <th>Editar</th>
                                <th>Excluir</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $grupo->getPermissoesEntidade() as $permissao}
                                <tr>
                                    <td>
                                        <input type="hidden" name="permissao_menu_id[]" value="{$permissao->getId()}"/>
                                        <input type="hidden" name="codigo_entidade[]" value="{$permissao->getCodigoEntidade()}"/>
                                        <input type="checkbox" name="is_selecinado_todos" value="1" class="marcaLinhaMenu" {if $permissao->getInserir() eq true and $permissao->getEditar() eq true and $permissao->getExcluir() eq true}checked{/if}/>
                                    </td>
                                    <td class="text-left">
                                        {App\Model\PermissaoEntidade::getEntidade($permissao->getCodigoEntidade())}
                                    </td>
                                    <td>
                                        <input type="checkbox" name="is_selecinado_inserir[{$permissao->getCodigoEntidade()}]" value="1" {if $permissao->getInserir() eq true}checked{/if}/>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="is_selecinado_editar[{$permissao->getCodigoEntidade()}]" value="1" {if $permissao->getEditar() eq true}checked{/if}/>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="is_selecinado_excluir[{$permissao->getCodigoEntidade()}]" value="1" {if $permissao->getExcluir() eq true}checked{/if}/>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="usuariosTab" role="tabpanel">
                    {include file="../../Grupo/Templates/usuarios.tpl"}
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col ml-auto">
            <button type="submit" class="btn btn-primary ladda-button"> <i class="fa fa-save"></i> Salvar</button>
            {if $grupo->getId() neq ""}
                <a class="btn btn-danger btn-excluir" title="Excluir" href="{$app_url}grupo/excluir/id/{$grupo->getId()}"><i class="fa fa-trash-o"></i> Excluir</a>
            {/if}
            <a class="btn btn-light border btn-loading" href="{$app_url}grupo"><i class="fa fa-times"></i> Cancelar</a>
        </div>
        <div class="col-md-4 mr-auto text-right">
            {if $grupo->getId() neq ""}
                <p class="form-control-static text-muted">
                    Data cadastro registro: {$grupo->getDataCadastro()->format('d/m/Y')}<br/>
                    Última alteração: 
                    {if $grupo->getUltimaAlteracao() neq ""}
                        {$grupo->getUltimaAlteracao()->format('d/m/Y')} às {$grupo->getUltimaAlteracao()->format('H:i')}
                    {else}
                        Não registrado
                    {/if}
                </p>
            {/if}
        </div>
    </div>
</form>
