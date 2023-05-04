<div id="adc-anexos-modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Anexos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Protocolo: {$processo_n}</h6>
                <form id="formAnexoMulti" class="form-horizontal" enctype="multipart/form-data" method="POST"
                      action="{$app_url}Anexo/inserir/multiplos">
                    <input type="text" name="processo_id" value="{$processo->getId()}" class="hidden">
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">Tipo:</label>
                            <a id="cadastrar:TipoAnexo" href_select="select_tipo_documento_multi" href="#"
                               title="Cadastrar novo Tipo de Documento" class="btn btn-xs btn-success pull-right btn-cadastrar-modal"><i
                                        class="fa fa-plus"></i></a>
                            <select id="select_tipo_documento_multi" name="tipo_documento_id" class="form-control select2" required>
                                <option value="">Selecione</option>
                                {foreach $tipos_documentos as $tipo}
                                    <option value="{$tipo->getId()}">{$tipo->getDescricao()}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col">
                            <label>Classificação:</label>
                            <a id="cadastrar:Classificacao" href_select="select_classificacao_documento" href="#"
                               title="Cadastrar nova Classificação de Documento"
                               class="btn btn-xs btn-success pull-right btn-cadastrar-modal"><i class="fa fa-plus"></i></a>
                            <select id="select_classificacao_documento" name="classificacao_documento_id" class="form-control select2">
                                <option value="">Selecione</option>
                                {foreach $classificacoes as $classificacao}
                                    <option value="{$classificacao->getId()}">{$classificacao}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">Descrição:</label>
                            <input type="text" name="descricao_doc" id="descricao_doc" class="form-control form-control-sm"
                                   onkeyup="this.value = this.value.toUpperCase();"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">Data:</label>
                            <input autocomplete="off" type="text" name="data_doc"
                                   class="form-control form-control-sm datepicker" required/>
                        </div>
                        <div class="col">
                            <div style="display: flex;justify-content: space-between">
                                <label class="col-form-label">Número: <i data-toggle="popover" data-html="true" data-content="Informe os números dos documentos separados por &quot;,&quot; ou informe uma faixa numérica. Ex: 123,124,125 ou 123-125." class="fa fa-question-circle text-info tooltip-icon" data-original-title="" title=""></i></label>
                                <span class="col-form-label">
                                    Autonumerar?
                                    <input type="checkbox" id="auto_numero_doc" name="auto_numero_doc" value="0"
                                           onchange="alternarEntradaNumeroAnexo(this,'numero_doc_multi')"/>
                                </span>
                            </div>
                            <input id="numero_doc_multi" type="text" name="numero_doc" class="form-control form-control-sm"
                                   data-msg-pattern="Formato inválido. Informe número/ano."/>
                        </div>
                    </div>
                    {if !$usuarioEhInteressado}
                        <div class="form-group row">
                            <div class="col">
                                <label class="col-form-label mr-2">Mesclar documentos:</label>
                                <label class="switch">
                                    <input type="checkbox" name="merge" onchange="javascript:;">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    {/if}
                    {include file="../../Public/Templates/progress_bar.tpl"}
                    <div class="form-group">
                        {if $processo->getIsExterno()}
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_circulacao_interna" value="1" class="custom-control-input"
                                       id="checkBoxIsCirculacaoInterna">
                                <label class="custom-control-label" for="checkBoxIsCirculacaoInterna">
                                    Marque para não listar esse documento no processo externo (Documento de circulação interna).
                                </label>
                            </div>
                        {/if}
                        <div class="custom-control custom-checkbox">
                            <small class="form-text text-muted">* Tamanho máximo arquivo: {ini_get('post_max_size')}</small>
                            <small class="form-text text-muted">* Extensões permitidas: pdf, jpg, jpeg, gif, png, xsl, xslx, mp3 e mp4.</small>
                            <div id="kartik-file-errors"></div>
                            <div class="file-loading">
                                <input id="multi-arquivos" name="arquivos[]"  multiple type="file" data-show-caption="true"
                                       data-msg-placeholder="Selecione ou arraste...">
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button">
                            <i class="fa fa-save"></i> Salvar
                        </button>
                        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');">
                            <i class="fa fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script defer src="{$app_url}assets/js/view/anexo/multiplos_anexos.js"></script>