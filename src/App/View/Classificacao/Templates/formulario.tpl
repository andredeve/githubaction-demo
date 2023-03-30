{if $modal eq true}
    <h4 class='modal-title-dinamic'>{$page_title}</h4>
{/if}
<form id="{$entidade}Form" method="POST" action="{$app_url}{$entidade}/{$acao}" class="form-horizontal form-validate">
    <input type="hidden" name="entidade" value="{$entidade}"/>
    <input type="hidden" name="id" value="{$classificacao->getId()}"/>
    <div class="form-group">
        <label class="col-form-label">Sub-Classificação de:</label>
        <select class="select2" data-allow-clear="true" data-placeholder="Selecione" name="classificacao_pai_id">
            <option value="">Selecione</option>
            {foreach $classificacoes as $c}
                <option value="{$c->getId()}" {if $classificacao->getClassificacaoPai()->getId() eq $c->getId()}selected{/if}>{$c}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group row">
        <div class="col-3">
            <label class="col-form-label">Código:</label>
            <input type="text" name="codigo" class="form-control form-control-sm " value="{$classificacao->getCodigo()}"
                   required="true"/>
        </div>
        <div class="col">
            <label class="col-form-label">Título:</label>
            <input type="text" name="titulo" class="form-control form-control-sm  maiscula" value="{$classificacao->getTitulo()}"
                   required="true"/>
        </div>
    </div>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label">Fase Corrente:</label>
            <input type="text" name="faseCorrente" value="{$classificacao->getFaseCorrente()}" class="form-control form-control-sm "/>
        </div>
        <div class="col">
            <label class="col-form-label">Fase Intermediária:</label>
            <input type="text" name="faseIntermediaria" value="{$classificacao->getFaseIntermediaria()}"
                   class="form-control form-control-sm "/>
        </div>
        <div class="col">
            <label class="col-form-label">Destinação Final:</label>
            <select class="form-control form-control-sm " name="destinacaoFinal">
                <option value=""></option>
                {foreach \App\Enum\DestinacaoDocumento::getOptions() as $value=>$text}
                    <option value="{$value}"
                            {if $value eq $classificacao->getDestinacaoFinal()}selected{/if}>{\App\Enum\DestinacaoDocumento::getDescricao($value)}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label">Observações:</label>
        <textarea class="form-control form-control-sm " name="observacoes">{$classificacao->getObservacoes()}</textarea>
    </div>
    <hr/>
    <div class="form-group row">
        <div class="col ml-auto">
            <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
            {if $classificacao->getId() neq ""}
                <a class="btn btn-danger btn-excluir" title="Excluir"
                   href="{$app_url}{$entidade}/excluir/id/{$classificacao->getId()}"><i class="fa fa-trash-o"></i>
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
            {if $classificacao->getId() neq ""}
                <small class="form-text">
                    Data cadastro registro: {$classificacao->getDataCadastro()->format('d/m/Y')}<br/>
                    Última alteração:
                    {if $classificacao->getUltimaAlteracao() neq ""}
                        {$classificacao->getUltimaAlteracao()->format('d/m/Y')} às {$classificacao->getUltimaAlteracao()->format('H:i')}
                    {else}
                        Não registrado
                    {/if}
                </small>
            {/if}
        </div>
    </div>
</form>
