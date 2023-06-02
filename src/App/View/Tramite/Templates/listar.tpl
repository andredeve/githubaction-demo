{if $processo->getAssuntos()->count() gt 0}
    {$mostrar_assunto=true}
{else}
    {$mostrar_assunto=false}
{/if}
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead>
        <tr class="bg-light">
            <th></th>
            <th class="text-center">Fase</th>
            {if $mostrar_assunto eq true}
                <th>Assunto</th>
            {/if}
            <th>Data</th>
            <th>Hora</th>
            <th>Setor Origem</th>
            <th>Setor Destino</th>
            <th>Status</th>
            <th>Responsável</th>
            <th class="text-center">Tempo Gasto</th>
            <th>Recebido?</th>
        </tr>
        </thead>
        <tbody>
        {foreach $processo->getTramites() as $tramite}
            <tr tramite_id="{$tramite->getId()}" data-toggle="tooltip" data-placement="top"
                title="{$tramite->getParecer()}"
                class="{if $tramite->getIsCancelado() eq true}table-danger{elseif $processo->getNumeroFase(true) eq $tramite->getNumeroFase() and $tramite->getIsDespachado() eq false}ui-state-highlight{/if}">
                <td class="text-center vertical-middle">
                    <input type="hidden" name="tramite_id[]" value="{$tramite->getId()}"/>
                    <a entidade="tramite" title="Detalhar Informações de Trâmite" class="btn-detalhar text-success"
                       href="javascript:"><i class="fa fa-plus-circle fa-lg"></i></a>
                </td>
                <td class="text-center">{$tramite->getNumeroFase()}</td>
                {if $mostrar_assunto eq true}
                    <td class="text-left">{$tramite->getAssunto()}</td>
                {/if}
                <td class="text-center">{$tramite->getDataEnvio(true)}</td>
                <td class="text-center">{$tramite->getHoraEnvio()}</td>
                <td>{$tramite->getSetorAnterior()}</td>
                <td>
                    {$tramite->getSetorAtual()}
                    {if $tramite->getUsuarioDestino() neq null}
                        <br/>
                        <small class="text-info">{$tramite->getUsuarioDestino()->getPessoa()->getNome()}</small>
                    {/if}
                </td>
                <td class="text-left">
                    {if $tramite->getIsCancelado() eq true}
                        <span class="w-100 text-secondary"><i class="fa fa-times"></i> CANCELADO</span>
                    {else}
                        <span style="color: {$tramite->getStatus()->getCor()}"
                              class=" w-100">{$tramite->getStatus()->getDescricao()}</span>
                    {/if}
                    {*<br/>
                    <small class="text-muted">{$tramite->getParecer()}</small>*}
                </td>
                <td>{$tramite->getResponsavel()}</td>
                <td class="text-center">
                    {$tramite->getTempoGasto()}
                </td>
                <td class="text-center">
                    {if $tramite->getIsRecebido() eq 1}
                    <label class="badge badge-success">SIM</label>
                    {else}
                    <label class="badge badge-danger">NÃO</label>
                    {/if}<br/>
                </td>
            </tr>
            <tr id="detalhes-tramite-{$tramite->getId()}" class="hidden">
                <td colspan="{if $mostrar_assunto eq true}11{else}10{/if}" class="bg-light">
                    <table class="table table-sm bg-white">
                        <tr>
                            <th class="w-25">Vencimento:</th>
                            <td>
                                {$tramite->getVencimento()}
                            </td>
                        </tr>
                        <tr>
                            <th class="w-25">Data Envio:</th>
                            <td>
                                {$tramite->getDataEnvio(true,true)}
                            </td>
                        </tr>
                        <tr>
                            <th class="w-25">Usuário Envio:</th>
                            <td>
                                {$tramite->getUsuarioEnvio()}
                            </td>
                        </tr>
                        {if $tramite->getIsRecebido() eq true}
                            <tr>
                                <th class="w-25">Data Recebimento:</th>
                                <td>
                                    {$tramite->getDataRecebimento(true,true)}
                                </td>
                            </tr>
                            <tr>
                                <th class="w-25">Usuário Recebimento:</th>
                                <td>
                                    {$tramite->getUsuarioRecebimento()}
                                </td>
                            </tr>
                        {/if}
                        {if $tramite->getIsCancelado() eq true}
                            <tr>
                                <th class="w-25">Justificativa Cancelamento:</th>
                                <td>
                                    {$tramite->getJustificativaCancelamento()}
                                </td>
                            </tr>
                            <tr>
                                <th class="w-25">Usuário cancelamento:</th>
                                <td>
                                    {$tramite->getUsuarioCancelamento()}
                                </td>
                            </tr>
                        {/if}
                        <tr>
                            <th class="w-25">Parecer:</th>
                            <td>
                                {nl2br($tramite->getParecer())}
                            </td>
                        </tr>
                        {if $tramite->getRespostasCampo()->count() gt 0}
                            <tr>
                                <th class="w-25">Formulário respondido:</th>
                                <td>
                                    <table class="table table-sm">
                                        {foreach $tramite->getRespostasCampo() as $resposta}
                                            {if $resposta->getCampo()->getTipo() neq App\Enum\TipoCampo::ARQUIVO and $resposta->getCampo()->getTipo() neq App\Enum\TipoCampo::ARQUIVO_MULTIPLO}
                                                <tr>
                                                    <th class="w-25">{$resposta->getCampoTxt()}</th>
                                                    <td>
                                                        {if $resposta->getProcessoLincado() }
                                                            <a target="__banck" href="{$app_url}/Processo/editar/id/{$resposta->getProcessoLincado()->getId()}" >{$resposta->getResposta()}</a>
                                                        {else}
                                                            {$resposta->getResposta()}
                                                        {/if}
                                                    </td>
                                                </tr>
                                            {/if}
                                        {/foreach}
                                    </table>
                                </td>
                            </tr>
                        {/if}
                        {if $tramite->getRespostasPergunta()->count() gt 0}
                            <tr>
                                <th class="w-25">Respostas do Checklist:</th>
                                <td>
                                    <table class="table table-sm">
                                        {foreach $tramite->getRespostasPergunta() as $resposta}
                                            <tr>
                                                <th class="w-25">{$resposta->getPerguntaTxt()}</th>
                                                <td>
                                                    {if $resposta->getResposta() eq true}Sim{else}Não{/if}
                                                    {if $resposta->getObservacoes() neq null}
                                                        <br/>
                                                        <small class="text-muted">{$resposta->getObservacoes()}</small>
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                </td>
                            </tr>
                        {/if}
                        {if !empty($tramite->getDocumentosRequerimentosCadastrados()) }
                            <tr>
                                <th class="w-25">Documentos requeridos no próximo tramite:</th>
                                <td>
                                    <table class="table table-sm">
                                        {foreach $tramite->getDocumentosRequerimentosCadastrados() as $documento}
                                            <tr>
                                                <th class="w-25">{$documento->getAnexo()}</th>
                                                <td>
                                                    {if $documento->getIsObrigatorio() eq true} Arquivo obrigatório {else}-{/if}
                                                </td>
                                                <td>
                                                    {if $documento->getIsAssinaturaObrigatoria() eq true} Assinatura obrigatória {else}-{/if}
                                                </td>
                                                <td class="text-center">
                                                    {if ($documento->getUsuario() && $documento->getUsuario()->getId() == $usuario_logado->getId()) OR $usuario_logado->isAdm() }
                                                    
                                                        <div class="btn-group dropleft">
                                                            <button title="Ações disponíveis" type="button" class="btn btn-light border btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  <i class="fa fa-cogs"></i></button> 
                                                            <div class="dropdown-menu" style="">
                                                                <a  class="dropdown-item btn-excluir" title="Excluir" href="{$app_url}DocumentoRequerido/excluir/id/{$documento->getId()}"><i class="fa fa-trash-o"></i> Excluir</a>
                                                            </div>
                                                        </div>
        
                                                    {/if}
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                </td>
                            </tr>
                        {/if}
                    </table>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>