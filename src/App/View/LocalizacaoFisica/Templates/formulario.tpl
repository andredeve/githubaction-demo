<form id="localizacaoFisicaForm" class="form-horizontal form-validate" method="POST"
      action="{$app_url}localizacaoFisica/{$acao}">
    <input type="hidden" name="id" value="{$localizacao->getId()}"/>
    <div class="form-group row">
        <div class="col-4">
            <label class="col-form-label">Protocolo:</label>
            <select id="select_processo_arquivo" data-placeholder="Selecione" class="form-control form-control-sm"
                    name="processo_id">
            </select>
            <small class="form-text text-muted">Selecione o processo (numero/ano) que será arquivado .
            </small>
        </div>
        <div class="col">
            <label class="col-form-label">Exercício Protocolo:</label>
            <input type="text" id="exercicioDocumento" name="exercicioDocumento" class="form-control form-control-sm"
                   value="{$localizacao->getExercicioDocumento()}" required/>
        </div>
        <div class="col">
            <label class="col-form-label">Número Protocolo:</label>
            <input type="number" id="numeroDocumento" name="numeroDocumento" class="form-control form-control-sm"
                   value="{$localizacao->getNumeroDocumento()}" required/>
        </div>
        <div class="col">
            <label class="col-form-label">Data Protocolo:</label>
            <input type="text" id="dataDocumento" name="dataDocumento" class="form-control form-control-sm datepicker"
                   value="{$localizacao->getDataDocumento(true)}" required/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label">Ementa/ 1º Parágrafo:</label>
        <textarea class="form-control" id="ementa" name="ementa" rows="3"
                  required>{$localizacao->getEmenta()}</textarea>
    </div>
    <div class="row">
        <div class="col-7">
            <div class="form-group row">
                <div class="col-8">
                    <label class="col-form-label">Local:</label>
                    <select name="local_id" class="form-control select2 form-control-sm" required>
                        <option value=""></option>
                        {foreach $locais as $local}
                            <option value="{$local->getId()}"
                                    {if $localizacao->getLocal()->getId() eq $local->getId()}selected{/if}>{$local}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col">
                    <label class="col-form-label">Ref.:</label>
                    <input type="text" name="referencia_local" class="form-control form-control-sm"
                           value="{$localizacao->getRefLocal()}"/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-8">
                    <label class="col-form-label">Tipo de Local:</label>
                    <select name="tipolocal_id" class="form-control select2 form-control-sm" required>
                        <option value=""></option>
                        {foreach $tipos_local as $local}
                            <option value="{$local->getId()}"
                                    {if $localizacao->getTipoLocal()->getId() eq $local->getId()}selected{/if}>{$local}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col">
                    <label class="col-form-label">Ref.:</label>
                    <input type="text" name="referencia_tipo_local"
                           value="{$localizacao->getRefTipoLocal()}"
                           class="form-control form-control-sm"/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-8">
                    <label class="col-form-label">SubTipo de Local:</label>
                    <select name="subtipo_local_id" class="form-control select2 form-control-sm" required>
                        <option value=""></option>
                        {foreach $subtipos_local as $local}
                            <option value="{$local->getId()}"
                                    {if $localizacao->getSubTipoLocal()->getId() eq $local->getId()}selected{/if}>{$local}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col">
                    <label class="col-form-label">Ref.:</label>
                    <input type="text" name="referencia_subtipo_local"
                           value="{$localizacao->getRefSubTipoLocal()}"
                           class="form-control form-control-sm"/>
                </div>
            </div>
        </div>
        <div class="col">
            <label class="col-form-label">Observações:</label>
            <textarea name="observacoes_local_fisico" rows="7"
                      class="form-control">{$localizacao->getObservacao()}</textarea>
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col">
            <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i
                        class="fa fa-save"></i> Salvar
            </button>
            <a href="{$app_url}localizacaoFisica" class="btn btn-light border"><i class="fa fa-times"></i> Cancelar</a>
        </div>
        <div class="col-md-4 mr-auto text-right">
            {if $localizacao->getId() neq ""}
                <p class="form-control-static text-muted">
                    Data cadastro registro: {$localizacao->getDataCadastro()->format('d/m/Y')}<br/>
                    Última alteração:
                    {if $localizacao->getUltimaAlteracao() neq ""}
                        {$localizacao->getUltimaAlteracao()->format('d/m/Y')} às {$localizacao->getUltimaAlteracao()->format('H:i')}
                    {else}
                        Não registrado
                    {/if}
                </p>
            {/if}
        </div>

    </div>
</form>