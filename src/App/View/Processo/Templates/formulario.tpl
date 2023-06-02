{if isset($lxsign_url)}
    <input id="lxsign_url" type="hidden" value="{$lxsign_url}">
    <input id="access_token" class="hidden" value="{$access_token}">
{/if}
<form id="processoForm" class="form-horizontal" method="POST" enctype="multipart/form-data"
    action="{$app_url}Processo/{$acao}">
    <input id="acao_processo" type="hidden" name="acao" value="{$acao}" />
    <input id="processo_externo" type="hidden" name="processo_externo" value="{$usuarioEhInteressado}" />
    <input id="processo_id" type="hidden" name="id" value="{$processo->getId()}" />
    <input id="qtde_tramites" class="hidden" value="{$qtdeTramites}">
    {$tramiteAtual = $processo->getTramiteAtualSemApenso()}
    {if $processo->getId() !== null && $tramiteAtual}
        <input id="processo" type="hidden" name="processo" value="{$processo}" />
        <input id="tramite_id" type="hidden" name="tramite_id" value="{$processo->getTramiteAtualSemApenso()->getId()}" />
        <input id="abrirTramitacao" type="hidden" value="{$abrirTramitacao}">
    {/if}
    {if $acao eq 'inserir'}
        <div id="rootwizard" class="bwizard" processo_externo="{$usuarioEhInteressado}"
            setor_origem_id="{$processo->getSetorOrigem()->getId()}">
            <div>
                <ul class="nav nav-pills nav-fill">
                    {$ordem_wizard = 1}
                    <li class="nav-item">
                        <a class="nav-link" href="#tabProcesso" data-toggle="tab">
                            <span class="badge badge-warning">{$ordem_wizard++}</span> Dados Gerais
                        </a>
                    </li>
                    {if !$usuarioEhInteressado}
                        <li class="nav-item">
                            <a class="nav-link" href="#tabDocumentos" data-toggle="tab">
                                <span class="badge badge-warning">{$ordem_wizard++}</span> Documentos
                            </a>
                        </li>
                    {/if}
                    <li class="nav-item">
                        <a class="nav-link" href="#tabRequisitos" data-toggle="tab">
                            <span class="badge badge-warning">{$ordem_wizard++}</span> Requisitos para Abertura
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tabTramitar" data-toggle="tab">
                            <span class="badge badge-warning">{$ordem_wizard++}</span> Encaminhar
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <br />
                <div class="tab-pane" id="tabProcesso" role="tabpanel">
                    {include file="../../Processo/Templates/campos.tpl"}
                </div>
                {if !$usuarioEhInteressado}
                    <div class="tab-pane" id="tabDocumentos" role="tabpanel">
                        <div class="text-right">
                            <a href="#" class="btn btn-success btn-cadastrar-anexo">
                                <i class="fa fa-plus"></i> Adicionar Documento
                            </a>
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#adc-anexos-modal">
                                <i class="fa fa-plus"></i> Adicionar Múltiplos Documentos
                            </button>
                        </div>
                        <hr />
                        <div id="divAnexos">
                            {include file="../../Anexo/Templates/listar.tpl"}
                        </div>
                    </div>
                {/if}
                <div class="tab-pane" id="tabRequisitos" role="tabpanel">

                </div>
                <div class="tab-pane" id="tabTramitar" role="tabpanel">
                </div>
                <br />
                <ul class="pagination wizard justify-content-end">
                    <li class="page-item previous first" style="display:none;">
                        <a class="page-link" href="javascript:;">Primeiro</a>
                    </li>
                    <li class="page-item previous">
                        <a class="page-link" href="javascript:;">
                            <i class="fa fa-chevron-left"></i> Anterior
                        </a>
                    </li>
                    <li class="page-item next last" style="display:none;">
                        <a class="page-link" href="javascript:;">Último</a>
                    </li>
                    <li class="page-item next">
                        <a class="page-link" href="javascript:;">
                            <i class="fa fa-chevron-right"></i> Próximo
                        </a>
                    </li>
                    <li id="btn_submit" class="page-item finish" style="display:none;">
                        <a title="Finaliza e cadastra o processo." class="page-link" href="javascript:;"
                            onclick="$('#processoForm').submit();">
                            <i class="fa fa-check"></i> Finalizar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    {elseif $acao eq 'atualizar'}
        <p class="text-info pull-left lead">
            {if $processo->getNumero() != null}
                <i class="fa fa-file-o"></i> {$parametros['nomenclatura']} nº
                {$processo->getNumero()}/{$processo->getExercicio()}
            {/if}
            {if $processo->getSigilo() && $processo->getSigilo() != \App\Enum\SigiloProcesso::SEM_RESTRICAO}
                <span class="badge badge-danger"><i class="fa fa-lock">
                        {\App\Enum\SigiloProcesso::getOptions($processo->getSigilo())}</i></span>
            {/if}
            {if $processo->getIsArquivado() eq true}
                <span class="badge badge-secondary"><i class="fa fa-archive"> Arquivado</i></span>
            {/if}
        </p>
        <div class="pull-right">
            {*
            history.go da problema porque quando salva alguma coisa no processo, q não é por ajax, pq a 
            tela do processo se torna a anterior, assim ao clicar vai voltar para ela mesma
            *}
            <button type="button" onclick="history.go(-1);" class="btn btn-outline-secondary btn-loading">
                <i class="fa fa-arrow-left"></i> Voltar
            </button>
            {if !$usuarioEhInteressado}
                <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
            {/if}
            {if $pode_desarquivar }
                <a href="{$app_url}processo/desarquivar/id/{$processo->getId()}" class="btn btn-warning">
                    <i class="fa fa-folder-open-o"></i> Desarquivar
                </a>
            {/if}
            {if !$usuarioEhInteressado}
                <div class="btn-group">
                    <button title="Ações disponíveis" type="button" class="btn btn-success dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-file"></i> Ações
                    </button>
                    <div class="dropdown-menu">
                        {if $processo->getNumero() !== null && $tramiteAtual && ($tramiteAtual->getIsRecebido() == true && ($usuario_logado->getTipo() == App\Enum\TipoUsuario::MASTER or in_array($tramiteAtual->getSetorAtual()->getId(),$usuario_logado->getSetoresIds()))) }
                            <a id="btn-devolver-processo" href="javascript:;" processo="{$processo}"
                                tramite_id="{$processo->getTramiteAtualSemApenso()->getId()}" reload_page="true"
                                title="O Processo/Protocolo será devolvido ao setor de origem (setor de criação) e iniciará novamente o fluxograma na fase 1."
                                class="dropdown-item btn-devolver-processo">
                                <i class="fa fa-reply"></i> Devolver à Origem
                            </a>
                            <a id="btn-recusar-processo" href="javascript:;" processo="{$processo}"
                                tramite_id="{$processo->getTramiteAtualSemApenso()->getId()}" reload_page="true"
                                title="O Processo/Protocolo será devolvido ao setor anterior."
                                class="dropdown-item btn-recusar-processo">
                                <i class="fa fa-close"></i> Recusar
                            </a>
                            <a id="btn-tramitar-processo" href="javascript:;" processo="{$processo}" tramites="{$qtdeTramites}"
                                tramite_id="{$processo->getTramiteAtualSemApenso()->getId()}" reload_page="true"
                                title="O Processo/Protocolo será encaminhado ao próximo setor."
                                class="dropdown-item btn-tramitar-processo">
                                <i class="fa fa-send-o"></i> Tramitar
                            </a>
                        {elseif $processo->getNumero() !== null && $tramiteAtual && ($usuario_logado->getTipo() == App\Enum\TipoUsuario::MASTER or in_array($tramiteAtual->getSetorAtual()->getId(),$usuario_logado->getSetoresIds()))}
                            <a id="btn-receber-processo"
                                href="{$app_url}tramite/receber/id/{$processo->getTramiteAtualSemApenso()->getId()}"
                                processo="{$processo}" tramite_id="{$processo->getTramiteAtualSemApenso()->getId()}"
                                setor_atual="{$processo->getTramiteAtualSemApenso()->getSetorAtual()}" reload_page="true"
                                title="O Processo/Protocolo será recebido pelo setor atual."
                                class="dropdown-item btn-receber-processo">
                                <i class="fa fa-check"></i> Receber Processo
                            </a>
                        {/if}
                        {if $processo->getNumero() == null}
                            <a href="javascript:" processo_id="{$processo->getId()}" setores_id="{print_r($usuario_logado->getSetoresIds(true))}" id="btn_gerar_processo"
                                class="dropdown-item btn_gerar_processo">
                                <i class="fa fa-folder-open-o"></i> Gerar Processo
                            </a>
                        {else}
                            <a class="dropdown-item" title="Visualizar Processo Digital" target="_blank"
                                href="{$app_url}src/App/View/Processo/visualizar_digital.php?processo_id={$processo->getId()}">
                                <i class="fa fa-search"></i> Processo Digital
                            </a>
                            <a target="_blank" href="{$app_url}Processo/gerarRecibo/processo/{$processo->getId()}"
                                class="dropdown-item">
                                <i class="fa fa-file-text-o"></i> Gerar Recibo
                            </a>
                            <a title="Gerar capa para processo" target="_blank"
                                href="{$app_url}Processo/gerarCapa/processo/{$processo->getId()}" class="dropdown-item">
                                <i class="fa fa-lg fa-file"></i> Gerar Capa
                            </a>
                            <a target="_blank" href="{$app_url}Processo/gerarEtiqueta/processo/{$processo->getId()}"
                                class="dropdown-item">
                                <i class="fa fa-th-list"></i> Gerar Etiqueta
                            </a>
                        {/if}
                    </div>
                </div>
                <a class="btn btn-danger btn-excluir" title="Excluir"
                    href="{$app_url}Processo/excluir/id/{$processo->getId()}"><i class="fa fa-trash-o"></i> Excluir</a>
                {if $processo->getIsArquivado() eq false}
                    <a processo="{$processo}" processo_id="{$processo->getId()}" class="btn btn-light border btn-arquivar-processo"
                        title="Arquivar" href="javascript:;"><i class="fa fa-archive"></i> Arquivar</a>
                {/if}
            {/if}
        </div>
        <div class="clearfix"></div>
        {if $abrirTramitacao eq 1 AND count( $tramiteAtual->getRequirimentosObrigaroriosNaoCumpridos()) eq 0 AND count( $tramiteAtual->getRequirimentosSemObrigaroriedadeNaoCumpridos()) eq 0}
            <div>
                <p class="col alert alert-success">
                    Todos os documentos foram encaminhados. Por gentileza, dê prosseguimento ao processo.
                    <a href="#"
                        onclick="habilitarTramitacao({$processo->getTramiteAtualSemApenso()->getId()}, {$qtdeTramites}, '{$processo}')">ENCAMINHAR</a>.
                </p>
            </div>
        {elseif $abrirTramitacao eq 1 AND count( $tramiteAtual->getRequirimentosObrigaroriosNaoCumpridos()) gt 0}
            <div>
                <p class="col alert alert-danger">
                    Existem documentos obrigatórios pendentes de envio. Por gentileza, encaminhe-os para dar prosseguimento.<a href="#"
                        onclick="habilitarTramitacao({$tramiteAtual->getId()}, {$qtdeTramites}, '{$processo}')">ENCAMINHAR</a>.
                </p>
            </div>
        {elseif $abrirTramitacao eq 1 AND count( $tramiteAtual->getRequirimentosSemObrigaroriedadeNaoCumpridos()) gt 0}
            <div>
                <p class="col alert alert-warning">
                    Existem documentos não obrigatórios pendentes de envio. Por gentileza, encaminhe-os ou dê prosseguimento ao processo.<a href="#"
                        onclick="habilitarTramitacao({$tramiteAtual->getId()}, {$qtdeTramites}, '{$processo}')">ENCAMINHAR</a>.
                </p>
            </div>
        {/if}
        <hr class="m-1" />
        {if $processo->getIsArquivado() eq true}
            <div class="alert alert-info">
                Processo arquivado em {$processo->getDataArquivamento(true)}<br />
                <small><strong>Justificativa: </strong>{$processo->getJustificativaEncerramento()}</small>
            </div>
        {/if}
        {include file="../../Processo/Templates/campos.tpl"}
    {/if}
</form>
<!-- Excluir documento - Modal -->
<div data-app-url="{$app_url}" class="modal fade" id="modal-delete-attach" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Remover anexo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col">
                        <label class="col-form-label text-capitalizebody" for="motivo">Motivo: </label>
                        <textarea id="motivoText" class="form-control" rows="5" required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <input type="button" id="btn-delete-attach" class="btn btn-danger btn-delete-attach" value="Excluir">
            </div>
        </div>
    </div>
</div>
<!-- Excluir documento - Modal - END -->
<!-- Adicionar múltiplos arquivos - Modal -->
{include file="../../Anexo/Templates/formulario_multi.tpl" tipos_documentos=$tipos_documentos classificacoes=$classificacoes}
<!-- Adicionar múltiplos arquivos - Modal - END -->
<script defer src="{$app_url}assets/js/view/processo/editar.js"></script>