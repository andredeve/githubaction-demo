<input type="hidden" name="localizacao_fisica_id"
       value="{$processo->getLocalizacaoFisica()->getId()}"/>
<fieldset {if isset($disabled_localizacao)}disabled{/if}>
    <div class="row">
        <div class="col-7">
            <div class="form-group row">
                <div class="col-8">
                    <label class="col-form-label">Local:</label>
                    <select name="local_id" class="form-control select2 form-control-sm">
                        <option value=""></option>
                        {foreach $locais as $local}
                            <option value="{$local->getId()}"
                                    {if $processo->getLocalizacaoFisica()->getLocal()->getId() eq $local->getId()}selected{/if}>{$local}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col">
                    <label class="col-form-label">Ref.:</label>
                    <input type="text" name="referencia_local" class="form-control form-control-sm"
                           value="{$processo->getLocalizacaoFisica()->getRefLocal()}"/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-8">
                    <label class="col-form-label">Tipo de Local:</label>
                    <select name="tipolocal_id" class="form-control select2 form-control-sm">
                        <option value=""></option>
                        {foreach $tipos_local as $local}
                            <option value="{$local->getId()}"
                                    {if $processo->getLocalizacaoFisica()->getTipoLocal()->getId() eq $local->getId()}selected{/if}>{$local}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col">
                    <label class="col-form-label">Ref.:</label>
                    <input type="text" name="referencia_tipo_local"
                           value="{$processo->getLocalizacaoFisica()->getRefTipoLocal()}"
                           class="form-control form-control-sm"/>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-8">
                    <label class="col-form-label">SubTipo de Local:</label>
                    <select name="subtipo_local_id" class="form-control select2 form-control-sm">
                        <option value=""></option>
                        {foreach $subtipos_local as $local}
                            <option value="{$local->getId()}"
                                    {if $processo->getLocalizacaoFisica()->getSubTipoLocal()->getId() eq $local->getId()}selected{/if}>{$local}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col">
                    <label class="col-form-label">Ref.:</label>
                    <input type="text" name="referencia_subtipo_local"
                           value="{$processo->getLocalizacaoFisica()->getRefSubTipoLocal()}"
                           class="form-control form-control-sm"/>
                </div>
            </div>
        </div>
        <div class="col">
            <label class="col-form-label">Observações:</label>
            <textarea name="observacoes_local_fisico" rows="7"
                      class="form-control">{$processo->getLocalizacaoFisica()->getObservacao()}</textarea>
        </div>
    </div>
</fieldset>
