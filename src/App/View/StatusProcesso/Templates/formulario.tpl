<form id="statusForm" class="form-horizontal form-validate" method="POST" action="{$app_url}statusProcesso/{$acao}">
    <input type="hidden" name="id" value="{$status->getId()}"/>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Descrição:</label>
            <input type="text" placeholder="Nome do status" autofocus="true" class="form-control form-control-sm"
                   value="{$status->getDescricao()}" name="descricao" required="true">
        </div>
        <div class="col-md-1">
            <label class="col-form-label required">Cor:</label>
            <input type="color" placeholder="cor do status" class="form-control form-control-sm"
                   value="{$status->getCor()}" name="cor" required="true">
        </div>
        <div class="col-2">
            <label class="col-form-label">Status de Arquivamento:</label><br/>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioInline1" name="isArquivamento" value="1"
                       {if $status->getIsArquivamento() eq true}checked{/if} class="custom-control-input">
                <label class="custom-control-label" for="customRadioInline1">Sim</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="customRadioInline2" name="isArquivamento" value="0"
                       {if $status->getIsArquivamento() eq false}checked{/if} class="custom-control-input">
                <label class="custom-control-label" for="customRadioInline2">Não</label>
            </div>
        </div>
        <div class="col-3">
            <label class="col-form-label">Status de Devolvido à Origem:</label><br/>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="devolvidoOrigem1" name="isDevolvidoOrigem" value="1"
                       {if $status->getIsDevolvidoOrigem() eq true}checked{/if} class="custom-control-input">
                <label class="custom-control-label" for="devolvidoOrigem1">Sim</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="devolvidoOrigem2" name="isDevolvidoOrigem" value="0"
                       {if $status->getIsDevolvidoOrigem() eq false}checked{/if} class="custom-control-input">
                <label class="custom-control-label" for="devolvidoOrigem2">Não</label>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i>
            Salvar
        </button>
        <a href="{$app_url}statusProcesso" class="btn btn-light border"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</form>




