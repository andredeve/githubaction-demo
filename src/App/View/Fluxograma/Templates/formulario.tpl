<form id="fluxogramaForm" method="POST" action="{$app_url}fluxograma/{$acao}" class="form-horizontal form-validate">
    <input type="hidden" name="id" value="{$fluxograma->getId()}"/>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Assunto:</label>
            <select name="assunto_id" class="form-control select2" required>
                <option value="">Selecione</option>
                {if $fluxograma->getId() neq ""}
                    <option value="{$fluxograma->getAssunto()->getId()}"
                            selected>{$fluxograma->getAssunto()->getDescricao()}</option>
                {/if}
                {foreach $assuntos as $assunto}
                    <option value="{$assunto->getId()}">{$assunto->getDescricao()}</option>
                {/foreach}
            </select>
        </div>
        <div class="col">
            <label class="col-form-label">Fluxograma ativo?</label><br/>
            <div class="form-check form-check-inline">
                <label class="custom-control custom-radio">
                    <input id="fluxogramaAtivo" name="isAtivo" value="1" type="radio" class="custom-control-input"
                           {if $fluxograma->getIsAtivo() eq true}checked{/if}>
                    <label class="custom-control-label" for="fluxogramaAtivo">Sim</label>
                </label>
            </div>
            <div class="form-check form-check-inline">
                <label class="custom-control custom-radio">
                    <input id="fluxogramaInativo" name="isAtivo" value="0" type="radio" class="custom-control-input"
                           {if $fluxograma->getIsAtivo() eq false}checked{/if}>
                    <label class="custom-control-label" for="fluxogramaInativo">Não</label>
                </label>
            </div>
            <small class="text-muted">*Define se novos processos do assunto vinculado seguirão o fluxograma.</small>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <input id="setor_id" type="hidden" name="setores_id" value=""/>
                <div class="card-header">
                    <span class="lead">Adicionar Fase <span
                                id="fase_atual_text">{count($fluxograma->getFases())+1}</span></span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="col-form-label">Setor(es) da fase:</label>
                        <input id="search_setor" placeholder="Digite o setor para buscar" type="text"
                               class="form-control form-control-sm"/>
                        <small class="form-text text-muted">
                            *O(s) setor(es) selecionado(s) receberá(ão) o processo nesta fase.
                        </small>
                        <div id="jstree"></div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a href="#" class="btn btn-success btn-sm btn-adicionar-fase"><i class="fa fa-plus"></i>
                        Adicionar</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <span class="lead">Fases do Processo</span>
                </div>
                <table id="tabelaFases" class="table table-bordered table-sm">
                    <thead class="bg-light">
                    <tr>
                        <th class="text-center bg-light">Fase</th>
                        <th>Setor(es)<br/>
                            <small class="form-text text-muted">*Este setor(es) receberá(ão) o processo ao
                                simultaneamente neste fase.
                            </small>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $fluxograma->getFases() as $indice_f=>$fase}
                        <tr class="linha-fase">
                            <th class="text-center bg-light" style="vertical-align: middle">
                                <input type='hidden' class="fase-id" name='fase_id[]' value="{$fase->getId()}"/>
                                <input type='hidden' class="fase-numero" name='fase[]' value="{$fase->getNumero()}"/>
                                <span class="fase-numero-text">{$fase->getNumero()}</span>ª
                            </th>
                            <td style="padding: 0px;">
                                {$setores_fase=$fase->getSetoresFase()}
                                <table class="tabela-fase table table-sm">
                                    {foreach $setores_fase as $indice_s=>$setor_fase}
                                        <tr style="cursor: move">
                                            <td>
                                                <input type="hidden" name="setor_fase_id[{$indice_f}][]"
                                                       value="{$setor_fase->getId()}"/>
                                                <input type="hidden" name="setor_id[{$indice_f}][]"
                                                       value=" {$setor_fase->getSetor()->getId()}"/>
                                                {$setor_fase->getSetor()->getNome()}
                                            </td>
                                            <td style="width: 80px">
                                                <input placeholder="Prazo" type="number" name="prazo[{$indice_f}][]"
                                                       value="{$setor_fase->getPrazo()}"
                                                       class="form-control form-control-sm"/>
                                            </td>
                                            <td class="text-center" style="width: 85px">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox"
                                                               title="Marque para que o prazo seja contado em dias úteis"
                                                               name="is_dia_util[{$indice_f}][]"
                                                               {if $setor_fase->getIsPrazoDiaUtil() eq true}checked{/if}/>
                                                        Dia útil?
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </table>
                            </td>
                            <td class="text-center" style="vertical-align: middle">
                                <a id="a{$fase->getId()}" title='{if $fase->getAtivo() eq true}Des{else}Re{/if}ativar fase' href='javascript:' class='btn-{if $fase->getAtivo() eq true}des{else}re{/if}ativar-fase'>
                                    <label class="switch" style="margin-bottom: 0; margin-right: .3rem; vertical-align:middle">
                                    <input type="checkbox" id="check{$fase->getId()}" name='ativo[]' value='{$fase->getAtivo()}'{if $fase->getAtivo() eq true}checked{/if}>
                                    <span class="slider"></span>
                                    </label>
                                </a>

                                <a title='Remover fase' href='javascript:'
                                   class='btn btn-danger btn-xs btn-remover-fase'><i class='fa fa-times'></i></a>
                                   
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                <div class="card-footer">
                    <small class="text-muted">*Clique e arraste para mudar a ordem das fases.</small>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col ml-auto">
            <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
            {if $fluxograma->getId() neq ""}
                <a class="btn btn-warning" title="Gerenciar Estrutura de requisitos entre os trâmites"
                   href="{$app_url}fluxograma/estrutura/id/{$fluxograma->getId()}"><i class="fa fa-cogs"></i> Estrutura</a>
                <a class="btn btn-danger btn-excluir" title="Excluir"
                   href="{$app_url}fluxograma/excluir/id/{$fluxograma->getId()}"><i class="fa fa-trash-o"></i>
                    Excluir</a>
            {/if}
            <a class="btn btn-light border btn-loading" href="{$app_url}fluxograma"><i class="fa fa-times"></i>
                Fechar</a>
        </div>
        <div class="col-md-4 mr-auto text-right">
            {if $fluxograma->getId() neq ""}
                <p class="form-control-static text-muted">
                    Data cadastro registro: {$fluxograma->getDataCadastro()->format('d/m/Y')}<br/>
                    Última alteração:
                    {if $fluxograma->getUltimaAlteracao() neq ""}
                        {$fluxograma->getUltimaAlteracao()->format('d/m/Y')} às {$fluxograma->getUltimaAlteracao()->format('H:i')}
                    {else}
                        Não registrado
                    {/if}
                </p>
            {/if}
        </div>
    </div>
</form>
<script defer="" src="{$app_url}assets/js/view/fluxograma/formulario.js"></script>