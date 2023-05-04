<div class="app-overlay"></div>
<p class="invisible" id="app_url">{$app_url}</p>
<table id="tabelaAnexos" class="table table-bordered table-sm datatable">
    <thead class="bg-light">
    <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Descrição</th>
        <th>Data</th>
        <th>Número</th>
        <th class="hidden">Valor</th>
        <th>Qtde. Páginas</th>
        <th class="text-center {if $usuarioEhInteressado}hidden{/if}">Assinatura</th>
        <th class="hidden">OCR</th>
        <th class="text-center">Arquivo</th>
    </tr>
    </thead>
    <tbody>
    {$anexos=$processo->getAnexos()}
    {if !is_array($anexos)}
        {$anexos = $anexos->toArray()}
    {/if}
    {foreach $anexos as $i=>$anexo}
        {if $consultar && $anexo->getIsCirculacaoInterna() && !$usuario_logado }
           {continue}
        {/if}
        <tr>
            <td>{$anexo->getID()}</td>
            <td style="text-align: left!important;" pode-assinar="{$anexo->podeMandarParaAssinatura()}">{$anexo->getTipo()}{if in_array($anexo->getId(), $substituido)}<i class="fa fa-exclamation-triangle fa-lg text-warning" title="O arquivo desse anexo foi substituído. Para verificar as versões anteriores, cheque o histórico"></i>{/if}</td>
            <td>{$anexo->getDescricao()}</td>
            <td data-order="{if $anexo->getData() neq null}{$anexo->getData()->format('Y-m-d')}{/if}"
                class="text-center">{$anexo->getData(true)}</td>
            <td class="text-center">{$anexo->getNumero()}</td>
            <td class="hidden" data-order="{$anexo->getValor()}">{$anexo->getValor(true)}</td>
            <td class="text-center">{$anexo->getQtdePaginas()}</td>
            <td class="text-center {if $usuarioEhInteressado}hidden{/if}">
                {if isset($anexo->status)}
                    {if $anexo->status eq 'Finalizado'}
                        <span class="badge badge-success">
                            TOTALMENTE ASSINADO
                        </span>
                    {elseif $anexo->status eq 'Em Processo'}
                        <span class="badge badge-warning">
                            PENDENTE  DE ASSINATURA(S)
                        </span>
                    {elseif $anexo->status eq 'Excluído'}
                        <span class="badge badge-warning">
                            EXCLUÍDO
                        </span>
                    {elseif $anexo->status eq 'Pré-Cadastro'}
                        <span class="badge badge-warning">
                            PRÉ-CADASTRO
                        </span>
                    {elseif $anexo->status eq 'Cancelado'}
                        <span class="badge badge-secondary">
                            CANCELADO
                        </span>
                    {/if}
                {else}
                    <span class="badge badge-info" data-toggle="tooltip" data-placement="top" title="Sem requisição de assinatura(s).">
                        NÃO REQUER ASSINATURA
                    </span>
                {/if}
            </td>
            <td class="hidden">
                {if $anexo->getIsDigitalizado() eq true}
                    {if $anexo->getIsOCRFinalizado() eq true}
                        <span class="badge badge-success"><i class="fa fa-check"></i> Finalizado</span>
                    {elseif $anexo->getIsOCRIniciado() eq true}
                        <span class="badge badge-info"><i class="fa fa-spinner fa-pulse fa-fw"></i> Em Andamento</span>
                    {else}
                        <span class="badge badge-warning"><i class="fa fa-exclamation-triangle"></i> Pendente</span>
                    {/if}
                {else}
                    N/A
                {/if}
            </td>
            <td class="text-center">
                <div class="btn-group">
                   {if ($acao === 'atualizar' || $acao === 'visualizar')}
                        <button type="button" onclick="mostrarHistoricoAnexo(this)"
                           class="btn btn-xs btn-dark history-btn" data-id="{$anexo->getId()}">
                            <i class='fa fa-history'></i>
                        </button>
                    {/if}
                    {if is_file($anexo->getArquivo(false,false,true)) and strtolower($anexo->getExtensao()) eq 'pdf'}
                        {if file_exists($anexo->getArquivoOriginal())}
                            <div class="dropdown">
                                <a class="btn btn-xs btn-warning dropdown-toggle" href="#" role="button" id="dropdownVisualizar{$anexo->getId()}" data-toggle="dropdown" aria-expanded="false">
                                    <i class='fa fa-search'></i>
                                </a>

                                <div class="dropdown-menu" aria-labelledby="dropdownVisualizar{$anexo->getId()}">
                                    <a class="dropdown-item
                                       {if $anexo->getConverter() and $anexo->getConverter()->getPosicaoFila() != 0 }
                                           convertendo " title="{$anexo->getConverter()->getTextoFila()}"
                                       {else} "{/if}
                                        target="_blank" {if !is_null($anexo->getId())}href="{$app_url}anexo/abrirArquivoAnexo/{$anexo->getId()}" {else} href="{$app_url}anexo/abrirArquivoAnexo/{basename($anexo->getArquivoPath())}" {/if}>Arquivo
                                    </a>
                                    <a class="dropdown-item" target="_blank" href="{$anexo->getArquivoOriginal(true)}">Arquivo Original</a>
                                </div>
                            </div>
                        {else}
                            {if !is_null($anexo->getId())}
                                <a target="_blank" href="{$app_url}anexo/abrirArquivoAnexo/{$anexo->getId()}"
                                   class="btn btn-xs btn-warning"><i class='fa fa-search'></i></a>
                            {else}
                                <a target="_blank" href="{$app_url}anexo/abrirArquivoAnexo/{basename($anexo->getArquivoPath())}"
                                   class="btn btn-xs btn-warning"><i class='fa fa-search'></i></a>
                            {/if}

                        {/if}
                    {elseif $anexo->getArquivo() neq null and $anexo->isImage() eq true}
                        <a data-title="{$anexo}" data-lightbox="anexo_{$anexo->getId()}"
                           href="{$anexo->getPathUrl()}{$anexo->getArquivo()}" class="btn btn-xs btn-warning"><i
                                    class='fa fa-search'></i> </a>
                    {elseif !is_null($anexo->getArquivo()) and !is_null($anexo->getId())}
                        <a target="_blank" href="{$app_url}anexo/abrirArquivoAnexo/{$anexo->getId()}"
                           class="btn btn-xs btn-warning"><i class='fa fa-search'></i></a>
                    {else}
                        <a class='btn btn-warning btn-xs disabled' title="Sem arquivo anexado."><i
                                    class='fa fa-search'></i></a>
                    {/if}
                    {if !isset($consultar) && !$usuarioEhInteressado}

                        {if isset($app['lxsign']) && !empty($app['lxsign'])}
                            <a class="btn btn-success btn-xs btn-assinatura
                                {if $hasAttachAddPermission neq true} disabled {/if}" indice="{$i}"
                               anexo_id="{$anexo->getId()}" indice="{$i}" title="Enviar para assinatura">
                                <i class="fa  fa-paper-plane-o"></i>
                            </a>
                        {/if}
                        <div class="dropdown">
                            <a class="btn btn-xs btn-info dropdown-toggle" href="#" role="button" id="dropdownEditar{$anexo->getId()}" data-toggle="dropdown" aria-expanded="false">
                                <i class='fa fa-edit'></i>
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dropdownEditar{$anexo->getId()}">
                                <a editar_info=1 anexo_id="{$anexo->getId()}" indice="{$i}" is_digitalizado="{$anexo->getIsDigitalizado()}"
                                href="#" nome_arquivo="{$anexo->getArquivo()}" title="Editar informações do anexo"
                                class="dropdown-item btn-editar-anexo{if $hasAttachAddPermission neq true} disabled {/if}"> Editar informações </a>

                                <a editar_arquivo=1 anexo_id="{$anexo->getId()}" indice="{$i}" is_digitalizado="{$anexo->getIsDigitalizado()}"
                                href="#" nome_arquivo="{$anexo->getArquivo()}" title="Substituir arquivo"
                                class="dropdown-item btn-editar-anexo{if $hasAttachAddPermission neq true} disabled {/if}"> Substituir Arquivo </a>
                            </div>
                        </div>
                        {* <a anexo_id="{$anexo->getId()}" indice="{$i}" is_digitalizado="{$anexo->getIsDigitalizado()}"
                           href="#" nome_arquivo="{$anexo->getArquivo()}" title="Editar anexo"
                           class="btn btn-xs btn-info btn-editar-anexo {if $hasAttachAddPermission neq true} disabled {/if}">
                            <i class="fa fa-edit"></i>
                        </a> *}

                        <a data-toggle="modal"
                           processo-id="{$anexo->getProcesso()->getId()}"
                           processo-num="{$anexo->getProcesso()->getNumero()}"
                           anexo-id="{$anexo->getId()}" indice="{$i}"
                           href="#" nome-arquivo="{$anexo->getArquivo()}" title="Notificar usuários"
                           class="btn btn-xs btn-secondary btn-notificar-usuarios {if $hasAttachAddPermission neq true} disabled {/if}">
                            <i class="fa fa-bell"></i>
                        </a>

                        <a data-toggle="modal"
                           data-processo-id="{$anexo->getProcesso()->getId()}"
                           data-anexo-id="{$anexo->getId()}" data-indice="{$i}"
                           href="#" data-nome-arquivo="{$anexo->getArquivo()}" title="Remover anexo"
                           class="btn btn-xs btn-danger btn-excluir-anexo {if $hasAttachAddPermission neq true} disabled {/if}">
                            <i class="fa fa-trash"></i>
                        </a>
                        
                    {/if}
                </div>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
<!-- History Modal -->
<div class="modal modal-history fade" id="historic-modal" tabindex="-1" role="dialog" aria-labelledby="historic-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historic-modal-label">Histórico</h5>
                <button type="button" class="close" onclick="dismiss('.modal-history')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="qa-message-list" id="wall-history">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- LxSign Modal Base -->
<div id="modal-box">
    <div class="modal" id="lxsign-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="{$app_url}assets/img/logo-lxsign.png" width="30px"><h5 class="modal-title" id="lxsign-modal-label">Assinatura Digital</h5>
                    <button type="button" class="close lxsign-modal-close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
</div>
<script defer src="{$app_url}assets/js/view/assinatura/sign_status.js?v={$file_version}"></script>