

<form id="reordenarForm" class="form-horizontal " method="POST" action="{$app_url}componente/{$acao}">
    <input type="hidden" name="componente_id" value="{$componente->getId()}"/>
    <input type="hidden" name="ajax" value="1"/>
    
    <div class="form-group">
        <label class="col-form-label required">Documento de Referência:</label>
        <select name="componente_referencia_id" class="select2 form-control" required="true" >
            <option value="">Selecione</option>
            {foreach $componentes as $comp}
                {if $componente->getId() != $comp->getId()}
                    <option value="{$comp->getId()}"  >                   
                            {$comp->getOrdem(true)} - 
                            {if $comp->getAnexo()}
                                {$comp->getAnexo()}
                            {else if $comp->getTramite()}
                               Formulário Eletrônico
                            {/if}
                    </option>
                {/if}
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label class="col-form-label required">
            Posicionar Documento 
            <a href="javascript:"  data-toggle="tooltip" data-placement="right" title='Escolha a posição do documento: ANTES ou DEPOIS do "Documento de Referência".'>
                <i class="fa fa-question-circle" aria-hidden="true"></i>
            </a>:
        </label>
        <select name="posicionar_anexo" class=" form-control" >
            <option value="antes">ANTES</option>
            <option value="depois">DEPOIS</option>
        </select>
    </div>
   
    
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</form>
