{if $modal eq true}
    <h4 class='modal-title-dinamic'>{$page_title}</h4>
{/if}
<form id="{$entidade}Form" method="POST" action="{$app_url}{$entidade}/{$acao}" class="form-horizontal form-validate">
    <input type="hidden" name="entidade" value="{$entidade}"/>
    <input type="hidden" name="id" value="{$categoria->getId()}"/>
    <div class="form-group">
        <label class="col-form-label">Descrição:</label>
        <input type="text" name="descricao" class="form-control form-control-sm"
               value="{$categoria->getDescricao()}"
               required="true"/>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col ml-auto">
            <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
            {if $categoria->getId() neq ""}
                <a class="btn btn-danger btn-excluir" title="Excluir"
                   href="{$app_url}{$entidade}/excluir/id/{$categoria->getId()}"><i class="fa fa-trash-o"></i>
                    Excluir</a>
            {/if}
            {if !isset($modal) ||  $modal eq false}
                <a class="btn btn-light border btn-loading" href="{$app_url}{$entidade}"><i class="fa fa-times"></i>
                    Cancelar</a>
            {else}
                <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                            class="fa fa-times"></i> Cancelar</a>
            {/if}

        </div>
        <div class="col-md-4 mr-auto text-right">
            {if $categoria->getId() neq ""}
                <small class="form-text">
                    Data cadastro registro: {$categoria->getDataCadastro()->format('d/m/Y')}<br/>
                    Última alteração:
                    {if $categoria->getUltimaAlteracao() neq ""}
                        {$categoria->getUltimaAlteracao()->format('d/m/Y')} às {$categoria->getUltimaAlteracao()->format('H:i')}
                    {else}
                        Não registrado
                    {/if}
                </small>
            {/if}
        </div>
    </div>
</form>
