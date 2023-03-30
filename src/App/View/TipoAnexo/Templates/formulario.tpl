{if $modal eq true}
    <h4 class='modal-title-dinamic'>{$page_title}</h4>
{/if}
<form id="tipoAnexoForm" class="form-horizontal form-validate" method="POST" action="{$app_url}tipoAnexo/{$acao}">
    <input type="hidden" name="id" value="{$tipoAnexo->getId()}"/>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Descrição:</label>
            <input type="text" autofocus="true" class="form-control form-control-sm" value="{$tipoAnexo->getDescricao()}" name="descricao" {if $tipoAnexo->getId() neq null} disabled="disabled" {/if}  required="true">
        </div>
    </div>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label mr-2">
                Ativo:
                <i data-toggle="popover" data-html="true" class="fa fa-question-circle text-info tooltip-icon"
                   data-original-title="" title="" data-content="Ativar/desativar um documento.">
                </i>
            </label>
            <label class="switch">
                <input type="checkbox" name="ativo" {if $acao eq 'atualizar' && $tipoAnexo->getAtivo() eq true}checked{/if} onchange="javascript:;">
                <span class="slider round"></span>
            </label>

            <label class="col-form-label ml-5 mr-2">
                Altera o vencimento do {$parametros['nomenclatura']}:
                <i data-toggle="popover" data-html="true" class="fa fa-question-circle text-info tooltip-icon"
                   data-original-title="" title="" data-content="Marque caso o tipo de anexo altere o vencimento do {$parametros['nomenclatura']}.">
                </i>
            </label>
            <label class="switch">
                <input type="checkbox" name="alteraVencimento" {if $tipoAnexo->getAlteraVencimento() eq true}checked{/if} onchange="javascript:;">
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
        {if !isset($modal) ||  $modal eq false}
            <a href="{$app_url}tipoAnexo" class="btn btn-light border"><i class="fa fa-times"></i> Cancelar</a>
        {else}
            <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i class="fa fa-times"></i> Cancelar</a>
        {/if}
    </div>
</form>




