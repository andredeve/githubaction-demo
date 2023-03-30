{if $modal eq true}
    <h4 class='modal-title-dinamic'>{$page_title}</h4>
{/if}
<form id="localForm" class="form-horizontal form-validate" method="POST" action="{$app_url}tipoLocal/{$acao}">
    <input type="hidden" name="id" value="{$local->getId()}"/>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Descrição:</label>
            <input type="text" autofocus="true" class="form-control form-control-sm" value="{$local->getDescricao()}"
                   name="descricao" required="true">
        </div>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col">
            <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i
                        class="fa fa-save"></i> Salvar
            </button>
            {if !isset($modal) || $modal eq false}
                <a href="{$app_url}tipoLocal" class="btn btn-light border"><i class="fa fa-times"></i> Cancelar</a>
            {else}
                <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                            class="fa fa-times"></i> Cancelar</a>
            {/if}
        </div>
        <div class="col-md-4 mr-auto text-right">
            {if $local->getId() neq ""}
                <p class="form-control-static text-muted">
                    Data cadastro registro: {$local->getDataCadastro()->format('d/m/Y')}<br/>
                    Última alteração:
                    {if $local->getUltimaAlteracao() neq ""}
                        {$local->getUltimaAlteracao()->format('d/m/Y')} às {$local->getUltimaAlteracao()->format('H:i')}
                    {else}
                        Não registrado
                    {/if}
                </p>
            {/if}
        </div>

    </div>
</form>




