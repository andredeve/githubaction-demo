<form id="formDocumentoRequerido" class="form-horizontal" method="POST" action="{$app_url}documentoRequerido/{$acao}">
    <input type="hidden" name="id" value="{$documentoRequerido->getId()}"/>
    <input type="hidden" name="ajax" value="true"/>
    <input type="hidden" name="acao" value="{$acao}"/>
    <input type="hidden" name="tramite_cadastro_id" value="{$documentoRequerido->getTramiteCadastro()->getId()}"/>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Anexo: </label>
            <a id="cadastrar:Anexo" processo-id='{$documentoRequerido->getTramiteCadastro()->getProcesso()->getId()}' href_select="select_anexo" href="#"
               class="btn btn-xs btn-success float-right  btn-cadastrar-anexo-requerido"><i class="fa fa-plus"></i></a>
            <select id="select_anexo" href_select='select_anexo' class="form-control form-control-sm select2" name="anexo_id" required>
                <option value=""></option>
                {foreach $anexos as $anexo}
                    <option value="{$anexo->getId()}"
                            {if $documentoRequerido->getAnexo() and $anexo->getId() eq $documentoRequerido->getAnexo()->getId()}selected{/if}>{$anexo}</option>
                {/foreach}
            </select>
        </div>
       
    </div>
    <div class="form-group row">
        <div class="col">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="isObrigatorio" value="1" class="custom-control-input" id="isObrigatorioId" {if $documentoRequerido->getIsObrigatorio() }checked="true"  {/if} >
                <label class="custom-control-label " for="isObrigatorioId"> Marque caso sejá obrigatório adiconar o documento. </label>
            </div>
        </div>
    </div>
    <div class="form-group row" id="checkbox_assign">
        <div class="col">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="isAssinaturaObrigatoria" value="1" class="custom-control-input" id="isAssinaturaObrigatoria" {if $documentoRequerido->getIsObrigatorio() }checked="true"  {/if}>
                <label class="custom-control-label" for="isAssinaturaObrigatoria"> Marque caso sejá obrigatório assinar o documento. </label>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i>
            Salvar
        </button>
        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                    class="fa fa-times"></i> Cancelar</a>
    </div>
</form>





