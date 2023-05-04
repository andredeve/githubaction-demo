{if $modal eq true}
    <h4 class='modal-title-dinamic'>{$page_title}</h4>
{/if}
<form id="assuntoForm" class="form-horizontal form-validate" method="POST" action="{$app_url}assunto/{$acao}">
    <input type="hidden" name="id" value="{$assunto->getId()}"/>
    {if !isset($modal) || $modal eq false}
        <div class="form-group row">
            <div class="col-3">
                <label class="col-form-label">Ativo?
                </label><br/>
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-radio">
                        <input id="radioAtivo1" type="radio" name="isAtivo" value="1" class="custom-control-input" {if $assunto->getIsAtivo() eq true}checked{/if}>
                        <label class="custom-control-label" for="radioAtivo1">Sim</label>
                    </div>
                </div>
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-radio">
                        <input id="radioAtivo2" type="radio" name="isAtivo" value="0" class="custom-control-input" {if $assunto->getIsAtivo() eq false}checked{/if}>
                        <label class="custom-control-label" for="radioAtivo2">Não</label>
                    </div>
                </div>
            </div>
                        
            <div class="col-3">
                <label class="col-form-label">Externo?
                    <i data-toggle="popover" data-html="true" data-content="Quando essa opção estiver marcada o assunto ficará disponível para interação com os interessados. " class="fa fa-question-circle text-info tooltip-icon"></i>
                </label><br/>
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-radio">
                        <input id="radioExterno1" type="radio" name="isExterno" value="1" class="custom-control-input" {if $assunto->getIsExterno() eq true}checked{/if}>
                        <label class="custom-control-label" for="radioExterno1">Sim</label>
                    </div>
                </div>
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-radio">
                        <input id="radioExterno2" type="radio" name="isExterno" value="0" class="custom-control-input" {if $assunto->getIsExterno() eq false}checked{/if}>
                        <label class="custom-control-label" for="radioExterno2">Não</label>
                    </div>
                </div>
            </div>        
        </div>
        <div class="form-group">
            <label class="col-form-label">Sub Assunto de: <strong>{$assunto->getAssuntoPai()->getDescricao()}</strong></label>
            <select class="form-control select2" name="assunto_pai_id">
                <option value="">Selecione</option>
                {foreach $assuntos as $a}
                    {if $a->getId() neq $assunto->getId()}
                        <option value="{$a->getId()}">{$a->getDescricao()}</option>
                    {/if}
                {/foreach}
            </select>
        </div>
        <div class="form-group">
            <label class="col-form-label">Setores atribuídos:</label>
            <select id="select_setores_usuario" name="setores_id[]" class="select2Tree" multiple="true">
                {include file="../../Setor/Templates/select.tpl"}
            </select>
        </div>
    {else}
        <input type="hidden" name="isAtivo" value="{$assunto->getIsAtivo()}"/>
        <input type="hidden" name="assunto_pai_id" value=""/>
    {/if}
    <div class="form-group">
        <label class="col-form-label required">Assunto:</label>
        <input type="text" autofocus="true" class="form-control maiscula text-uppercase" value="{$assunto->getDescricao()}" name="descricao" {if $assunto->getId() } disabled="true" {/if} required="true">
    </div>
    <div class="form-group">
        <label class="col-form-label">Prazo (dias):</label>
        <br/>
        <div class="input-group">
            <input type="number" class="form-control" name="prazo" required="true" value="{$assunto->getPrazo()}"/>
            <div class="input-group-append">
                <div class="input-group-text">
                    <div class="form-check form-check-inline">
                        <div class="custom-control custom-radio">
                            <input id="radioPrazo1" type="radio" name="isPrazoDiaUtil" value="0" class="custom-control-input" {if $assunto->getIsPrazoDiaUtil() eq false}checked{/if}>
                            <label class="custom-control-label" for="radioPrazo1">Corrido(s)</label>
                        </div>
                    </div>
                    <div class="form-check form-check-inline">
                        <div class="custom-control custom-radio">
                            <input id="radioPrazo2" type="radio" name="isPrazoDiaUtil" value="1" class="custom-control-input" {if $assunto->getIsPrazoDiaUtil() eq true}checked{/if}>
                            <label class="custom-control-label" for="radioPrazo2">Útei(s)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
        {if !isset($modal) ||  $modal eq false}
            <a href="{$app_url}assunto" class="btn btn-light border"><i class="fa fa-times"></i> Cancelar</a>
        {else}
            <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i class="fa fa-times"></i> Cancelar</a>
        {/if}
    </div>
</form>




