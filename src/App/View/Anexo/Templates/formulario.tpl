{if $usuarioEhInteressado}
    <h6>Protocolo: {$processo_n}</h6>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label">Tipo Documento:</label>
            <input type="text" value="{$anexo->getTipo()}" class="form-control form-control-sm" readonly/>
        </div>
        <div class="col">
            <label class="col-form-label">Descrição:</label>
            <input type="text" value="{$anexo->getDescricao()}" class="form-control form-control-sm" readonly/>
        </div>
    </div>
{else}
    <h6>Protocolo: {$processo_n}</h6>
    {if !$pode_editar}
        <p class="col alert alert-warning">
            Edição restrita. Para editar, é necessário que seja o responsável atual pelo processo e autor do anexo. Para atualizar o anexo, será necessário abrir uma solicitação aos administradores do sistema:  <a href="#" onclick="habilitarSolicitacaoEdicaoAnexo()">SOLICITAR ALTERAÇÃO</a>.
        </p>
    {/if}
{/if}
<!-- Formulário Novo Anexo de Documento -->
{if !$pode_editar and !is_null($anexo->getId())}
<form id="formAnexoEdicao" class="form-horizontal" enctype="multipart/form-data" method="POST" action="{$app_url}solicitacao/anexo/editar/{$anexo->getId()}">
    <div class="form-group row hidden form-anexo">
        <div class="col">
            <label class="col-form-label">Motivo:</label>
            <input type="text" name="motivo" class="form-control form-control-sm" disabled/>
        </div>
    </div>
    {else}
    <form id="formAnexo" class="form-horizontal" enctype="multipart/form-data" method="POST" action="{$app_url}Anexo/{$acao}">
        {if $requer_motivo eq 1 and !is_null($anexo->getId())}
            <div class="form-group row form-anexo">
                <div class="col">
                    <label class="col-form-label required">Motivo:</label>
                    <input type="text" name="motivo" class="form-control form-control-sm" required/>
                </div>
            </div>
        {/if}
{/if}
        <input type="hidden" name="id" value="{$anexo->getId()}"/>
        <input type="hidden" name="indice" value="{$indice}"/>
        <input type="hidden" name="processo_id" value="{$anexo->getProcesso()->getId()}"/>
        <input type="hidden" id="tipo_upload" name="tipo_upload"/>
        <input type="hidden" id="permitir_digitalizacao" name="permitir_digitalizacao" value="{$permitir_digitalizacao}"/>
{if ($acao eq 'atualizar' and $editar_arquivo eq 1)}<div class="hidden">{/if}
        <div class="form-group row {if $usuarioEhInteressado}hidden{/if}">
            <div class="col">
                <label class="col-form-label required">Tipo:</label>
                <a id="cadastrar:TipoAnexo" href_select="select_tipo_documento" href="#"
                   title="Cadastrar novo Tipo de Documento" class="btn btn-xs btn-success pull-right btn-cadastrar-modal"><i
                            class="fa fa-plus"></i></a>
                <select id="select_tipo_documento" name="tipo_documento_id" class="form-control select2" required {if !$pode_editar}disabled{/if}>
                    <option value="">Selecione</option>
                    {foreach $tipos_documento as $tipo}
                        <option value="{$tipo->getId()}"
                                {if $tipo->getId() eq $anexo->getTipo()->getId()}selected{/if}>{$tipo->getDescricao()}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col">
                <label class="col-form-label">Classificação:</label>
                <a id="cadastrar:Classificacao" href_select="select_classificacao_documento" href="#"
                   title="Cadastrar nova Classificação de Documento"
                   class="btn btn-xs btn-success pull-right btn-cadastrar-modal"><i class="fa fa-plus"></i></a>
                <select id="select_classificacao_documento" name="classificacao_documento_id" class="form-control select2"
                        {if !$pode_editar}disabled{/if}
                >
                    <option value="">Selecione</option>
                    {foreach $classificacoes as $classificacao}
                        <option value="{$classificacao->getId()}"
                                {if $classificacao->getId() eq $anexo->getClassificacao()->getId()}selected{/if}>{$classificacao}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group row {if $usuarioEhInteressado}hidden{/if}">
            <div id="divNovoVencimentoProcesso" class="col {if $anexo->getTipo()->getAlteraVencimento() eq false}d-none{/if}">
                <label class="col-form-label">Novo Vencimento:</label>
                <input type="text" class="form-control form-control-sm datepicker" name="novoVencimentoProcesso"
                       value="{$anexo->getNovoVencimentoProcesso(true)}" {if !$pode_editar}disabled{/if} />
                <small class="text-danger">Novo vencimento do {$parametros['nomenclatura']}.</small>
            </div>
            <div class="col">
                <label class="col-form-label">Descrição:</label>
                <input type="text" name="descricao_doc" id="descricao_doc" value="{$anexo->getDescricao()}"
                       class="form-control form-control-sm" {if !$pode_editar}disabled{/if}
                       onkeyup="this.value = this.value.toUpperCase();"/>
            </div>
        </div>
        <div class="form-group row {if $usuarioEhInteressado}hidden{/if}">
            <div class="col">
                <label class="col-form-label required">Data:</label>
                <input id="data_doc" autocomplete="off" value="{$anexo->getData(true)}" type="text" name="data_doc"
                       class="form-control form-control-sm datepicker" required {if !$pode_editar}disabled{/if}/>
            </div>
            <div class="col">
                <div style="display: flex;justify-content: space-between">
                    <label class="col-form-label">Número:</label>
                    <span class="col-form-label">
                Autonumerar?
                <input type="checkbox"
                       id="auto_numero_doc"
                       name="auto_numero_doc"
                       value="1"
                       checked
                       {if !$pode_editar}disabled{/if}
                        onchange="alternarEntradaNumeroAnexo(this,'numero_doc')"
                />
            </span>
                </div>
                <input id="numero_doc" type="text" value="{$anexo->getNumero()}" name="numero_doc"
                       {if !$pode_editar}disabled{/if}
                       disabled
                       class="form-control form-control-sm"  data-msg-pattern="Formato inválido. Informe número/ano."/>
            </div>
            <div class="col">
                <label class="col-form-label">Valor:</label>
                <input id="valor_doc" type="text" value="{$anexo->getValor(true)}" name="valor_doc"
                       class="form-control form-control-sm autonumeric" {if !$pode_editar}disabled{/if}/>
            </div>
            <div class="col">
                <label class="col-form-label">Páginas:</label>
                <input id="paginas_doc" type="number" min="1" value="{$anexo->getQtdePaginas()}" name="paginas_doc"
                       class="form-control form-control-sm" {if !$pode_editar}disabled{/if}/>
            </div>
        </div>
{if ($acao eq 'atualizar' and $editar_arquivo eq 1)}</div>{/if}
{if ($acao eq 'atualizar' and $editar_info eq 1)}<div class="hidden">{/if}
<div class="form-group row {if !$anexo->getArquivo() or $requer_motivo eq 1}hidden{/if}">
    <div class="col">
        <label class="col-form-label {if $anexo->getArquivo()}required{/if}">Motivo de substituição:</label>
        <input id="motivo" type="text" name="motivo"
                class="form-control form-control-sm" required/>
    </div>
</div>
        <div class="form-group row {if $usuarioEhInteressado or (!$pode_editar and !is_null($anexo->getId()))}hidden{/if}">
            <div class="col">
                <label class="col-form-label mr-2">Utilizar modelo:</label>
                <label class="switch">
                    <input id="useModelCheckbox" type="checkbox" name="useDocumentModel" onchange="javascript:;"
                           {if !$pode_editar}disabled{/if}
                    >
                    <span class="slider round"></span>
                </label>
            </div>
        </div>


        <div id="divArquivoProcesso" class="form-group">
            {if $assinatura and $assinatura->isAtivoNaAssinatura()}
                <div class="alert alert-danger">
                    Para editar o arquivo é necessário excluir o arquivo na assinatura.
                    <a target="_blanck" href="{$app_url}assinatura/visualizar/{$assinatura->getLxsign_id()}">Clique aqui para visualizar o documento no sistema de assinatura.</a></small>

                </div>
                {include file="../../Anexo/Templates/fileinput.tpl" disabled=true}
            {else}
                {include file="../../Anexo/Templates/fileinput.tpl" disabled=!$pode_editar}
            {/if}
        </div>


        {include file="../../Public/Templates/progress_bar.tpl"}
        <div class="form-group {if $usuarioEhInteressado}hidden{/if}">
            
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="is_circulacao_interna" value="1" class="custom-control-input"
                        {if $anexo->getIsCirculacaoInterna()}checked{/if} id="checkBoxIsCirculacaoInterna"
                        {if !$pode_editar}disabled{/if}
                >
                <label class="custom-control-label" for="checkBoxIsCirculacaoInterna">
                    Marque para não listar esse documento no processo na listagem pública.
                </label>
            </div>
            
            {if $anexo->getId() neq null and $anexo->getArquivo() neq null and $anexo->getExtensao()=='pdf'}
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="mesclar_arquivos" class="custom-control-input" id="checkBoxMesclarAnexo"
                           {if !$pode_editar}disabled{/if}
                    >
                    <label class="custom-control-label" for="checkBoxMesclarAnexo">
                        Marque para mesclar ao arquivo atual ao invés substituí-lo.
                    </label>
                </div>
                <small class="form-text text-info">
                    <strong>Atenção:</strong> a mesclagem dos arquivos só funcionará se os arquivos forem PDF's.
                </small>
            {/if}
        </div>

{if ($acao eq 'atualizar' and $editar_info eq 1)}</div>{/if}

        <hr/>
        <div class="form-group">
            <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"
                    {if !$pode_editar}disabled{/if}
            >
                <i class="fa fa-save"></i> Salvar
            </button>
            <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                        class="fa fa-times"></i> Cancelar</a>
        </div>
    </form>
    <script type="text/javascript" src="{$app_url}assets/js/view/processo/use_document_model.js?v={$file_version}"></script>


