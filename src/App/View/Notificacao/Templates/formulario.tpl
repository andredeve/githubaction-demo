{if $modal eq true}
    <h4 class='modal-title-dinamic'>{$page_title}</h4>
{/if}
<form id="notificacaoForm" class="form-horizontal form-validate-no-ignore-hidden" method="POST" action="{$app_url}notificacao/{$acao}">
    <input type="hidden" name="id" value="{$notificacao->getId()}"/>
    <div class="form-group" {if $modal eq true}hidden{/if}>
        <label class="col-form-label">Referente ao {$nomenclatura}: <span
                    class="text-muted">(não obrigatório)</span></label>
                    
        <div class="float-right">
            <a href="#" entidade="Processo" title="Pesquisa avançada por {$nomenclatura}" class="btn btn-xs btn-info btn-selectionar-entidade"><i class="fa fa-search"></i></a>
        </div>
        <select data-placeholder="Selecione" id="select_processo" name="processo_id" class="form-control"
                data-allow-clear="true">
            <option value="{if $modal eq true}{$processo->getId()}{/if}">{if $modal eq true}{$processo}{/if}</option>
        </select>
    </div>
    <div class="form-group">
        <label class="col-form-label">Destinatário:</label>
        <select id="select_destinatario" data-placeholder="Selecione" name="destinatario_id[]"
                class="form-control select2" multiple="true" required="true">
            <option value=""></option>
            {foreach $usuarios as $usuario}
                {if $usuario->getId() neq $usuario_logado->getId()}
                    <option value="{$usuario->getId()}">{$usuario->getPessoa()->getNome()}</option>
                {/if}
            {/foreach}
        </select>
    </div>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label">Assunto:</label>
            <input type="text" name="assunto" value="{if $modal eq true}Processo: {$processo} - Anexo: {$anexo->getNumero()}{else}{$notificacao->getAssunto()}{/if}" maxlength="255"
                   class="form-control form-control-sm" required/>
        </div>
        <div class="col-md-4">
            <label class="col-form-label">Prazo resposta (dias):</label>
            <br/>
            <div class="input-group input-group-sm">
                <input type="number" class="form-control " name="prazoDias" required="true"
                       value="{if $modal eq true}0{else}{$notificacao->getPrazoDias()}{/if}"/>
                <div class="input-group-append">
                    <div class="input-group-text form-control-sm">
                        <div class="form-check form-check-inline ">
                            <div class="custom-control custom-radio">
                                
                                <input id="radioPrazo1" type="radio" name="isPrazoDiaUtil" value="0"
                                       class="custom-control-input"
                                       {if $notificacao->getIsPrazoDiaUtil() eq false}checked{/if}>
                                <label class="custom-control-label" for="radioPrazo1">Corrido(s)</label>
                            </div>
                        </div>
                        <div class="form-check form-check-inline">
                            <div class="custom-control custom-radio">
                                <input id="radioPrazo2" type="radio" name="isPrazoDiaUtil" value="1"
                                       class="custom-control-input"
                                       {if $notificacao->getIsPrazoDiaUtil() eq true}checked{/if}>
                                <label class="custom-control-label" for="radioPrazo2">Útei(s)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label">Conteúdo da notificação:</label>
        <textarea name="texto" rows="4" class="form-control form-control-sm editor"
            required>{if $modal eq true}{foreach $conteudo as $texto}{$texto}<br/>{/foreach}{else}{$notificacao->getTexto()}{/if}</textarea>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i>
            Salvar
        </button>
        {if !isset($modal) || $modal eq false}
            <a href="{$app_url}notificacao" class="btn btn-light border"><i class="fa fa-times"></i> Cancelar</a>
        {else}
            <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                        class="fa fa-times"></i> Cancelar</a>
        {/if}
    </div>
</form>