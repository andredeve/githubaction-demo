{if isset($lxsign_url)}
    <input type="hidden" id="lxsign_url" value="{$lxsign_url}" />
{/if}
{if $usuarioIsExterno}
    <div id="divComentarioFinal">
        {if $tramite_inicial_contribuinte}
            <h5>
                <center>Clique em "Finalizar" para encaminhar o processo, caso precise fazer alguma alteração poderá clicar em
                    "Anterior" para voltar as abas.</center>
            </h5>
        {else}
            <h6>Existem documentos pendentes de envio. Por gentileza, encaminhe-os para dar prosseguimento.</h6>
        {/if}
    </div>
{/if}
{if isset($tramite) and $tramite neq null and $tramite->getDocumentosRequerimentosValidar() and  count( $tramite->getDocumentosRequerimentosValidar()) >0  }
    <div id="divDocumentosRequisitados">
        {include file="../../Tramite/Templates/alert_documentos_requeridos.tpl"}
    </div>
{/if}
{if isset($sem_tramites) and $sem_tramites eq true}
    <div class="alert alert-warning">
        Desculpe, o(s) processo(s) selecionado(s) possuem fluxograma definido e não serão encaminhados em
        massa.
    </div>
{else}
    {if isset($assunto) and $assunto->getFluxograma() neq null}
        {$fase=$assunto->getFluxograma()->getFases({$numero_fase})}
        {if $fase neq null}
            {$setores_fase=$fase->getSetoresFase()}
        {else}
            {$setores_fase=null}
        {/if}
        <div id="divFluxograma" {if $usuarioIsExterno}style="display: none;"{/if}>
            <span><b>Interessado: </b>{$processo->getInteressado(true)}</span>
            {include file="../../Tramite/Templates/fluxograma.tpl"}
        </div>
    {/if}
    {if $form eq true}
        <form id="tramitarProcessoForm" enctype="multipart/form-data" method="POST" action="{$app_url}processo/tramitar">
            {if isset($tramites)}
                <table class="table table-sm table-bordered table-hover" {if $usuarioIsExterno}style="display: none;" {/if}>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Processo</th>
                            <th>Assunto</th>
                            <th>Interessado</th>
                            <th>Setor Atual</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $tramites as $tramite}
                            {$processo=$tramite->getProcesso()}
                            <tr title="{$processo->getObjeto()}">
                                <td><input type="checkbox" name="tramite_id[]" value="{$tramite->getId()}" checked /></td>
                                <td>{$processo}</td>
                                <td>{$processo->getAssunto()}</td>
                                <td>{$processo->getInteressado(true)}</td>
                                <td>{$tramite->getSetorAtual()}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
                <input type="hidden" name="processo_id" value="{$processo->getId()}" />
                <input type="hidden" name="tramite_id" value="{if isset($tramite)}{$tramite->getId()}{/if}" />
                <input type="hidden" name="cancelar" value="{$cancelar}" />
                <input type="hidden" name="devolver" value="{$devolver}" />
                <input type="hidden" id="select_setor_origem" name="setor_origem_id"
                    value="{if isset($setor_origem)}{$setor_origem->getId()}{/if}" />
                {if $cancelar eq true}
                    {include file="cancelar.tpl"}
                {/if}
            {/if}
        {/if}
        {if isset($assunto) and $assunto->getFluxograma() neq null and $setores_fase eq null}
            {if $assunto->getSubAssuntos() neq null}
                <input type="hidden" name="arquivar" value="0" />
                <div id="divTomadaDecisao" class="form-group">
                    <label class="col-form-label">Prosseguir como:</label>
                    <select numero_fase="{$numero_fase}" processo_id="{$processo->getId()}" name="assuntoProsseguir"
                        class="form-control select-opcao-assunto" required>
                        <option value="">Selecione</option>
                        {foreach $assunto->getSubAssuntos() as $subAssunto}
                            <option value="{$subAssunto->getId()}">{$subAssunto->getDescricao()}</option>
                        {/foreach}
                    </select>
                    <small class="form-text text-info">O processo de <strong>{$assunto}</strong> chegou a sua fase final,
                        porém é necessário escolher como ele irá prosseguir.
                    </small>
                </div>
                <div id="divDestino">
                    {if isset($assunto) and count($temSetores) > 0}
                        {include file="../../Tramite/Templates/destino.tpl" setores=$temSetores}
                    {else}
                        {include file="../../Tramite/Templates/destino.tpl"}
                    {/if}
                </div>
            {else}
                {if !is_null($tramite) && $tramite->getForaFluxograma() eq false}
                    {include file="requisitos.tpl"}
                {/if}
                <input type="hidden" name="arquivar" value="1" />
                <div class="alert alert-warning">
                    O processo chegou a sua fase final. Neste momento ele será arquivado.
                </div>
                <div class="form-group">
                    <label class="required">Motivo do arquivamento:</label>
                    <textarea name="justificativa" class="form-control" rows="4" required="true">Fim de fluxograma.</textarea>
                    <small class="form-text text-muted">Descreva o porquê que esse processo está sendo arquivado neste
                        momento.
                    </small>
                </div>
                <fieldset>
                    <div class="form-group">
                        <label class="col-f orm-label">Apensar:</label>
                        <div class="float-right">
                            <a href="#" entidade="Processo" title="Pesquisa avançada por {$parametros['nomenclatura']}"
                                class="btn btn-xs btn-info btn-selectionar-entidade">
                                <i class="fa fa-search"></i>
                            </a>
                        </div>
                        <select class="form-control select_processo" name="processo_apensado">
                            <option></option>
                        </select>
                    </div>
                </fieldset>
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#"><i class="fa fa-folder-open-o"></i>
                                    Localização Física
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        {include file="../../Processo/Templates/localizacao.tpl"}
                    </div>
                </div>
            {/if}
        {else}
            <input type="hidden" name="arquivar" value="0" />
            {if isset($assunto) and count($temSetores) > 0}
                {include file="../../Tramite/Templates/destino.tpl" setores=$temSetores}
            {else}
                {include file="../../Tramite/Templates/destino.tpl"}
            {/if}
        {/if}
        {if isset($tramite) and !empty($tramite) and (!isset($disableRequimentoDeDocumentacao) or !$disableRequimentoDeDocumentacao)}
            <div class="divRequisitosProximoTramite {if $tramite->temFluxograma()} hidden{/if}">
                <hr>
                {include file="../../Tramite/Templates/documento_requerido.tpl"}
            </div>
            <div class="divArquivarProcesso hidden">
                <hr>
                {include file="../../Processo/Templates/arquivar.tpl" modal=true}
            </div>
        {/if}
        {if $form eq true}
            <hr />
            <div class="form-group row">
                {if !$usuarioIsExterno}
                    <div class="col">
                        {if $origem_unica eq true}
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="gerar_guia_tramitacao" value="1" class="custom-control-input"
                                    id="customCheckGTE">
                                <label class="custom-control-label" for="customCheckGTE">Gerar Guia de Remessa de
                                    Envio</label>
                            </div>
                        {/if}
                    </div>
                {/if}
                <div class="col text-right">
                    <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button">
                        {if isset($setores_fase)}
                            {if $setores_fase neq null}
                                <i class="fa fa-send-o"></i>
                                {if $usuarioIsExterno}
                                    Encaminhar
                                {else}
                                    Tramitar
                                {/if}
                            {else}
                                <i class="fa fa-archive"></i>
                                Arquivar
                            {/if}
                        {else}
                            <i class="fa fa-send-o"></i>
                            {if $usuarioIsExterno}
                                Encaminhar
                            {else}
                                Tramitar
                            {/if}
                        {/if}
                    </button>
                    {if $usuarioIsExterno}
                    <button type="button" data-style="expand-right" data-dismiss="modal" class="btn btn-warning ladda-button">
                        <i class="fa fa-times"></i>                        
                            Fechar
                    </button>
                    {/if}
                </div>
            </div>
        </form>
    {/if}
{/if}
<script type="text/javascript" defer src="{$app_url}assets/js/view/assinatura/formulario.js?v={$file_version}"></script>
<script defer type="text/javascript" src="{$app_url}assets/js/view/tramite/tramitar.js?v={$file_version}"></script>