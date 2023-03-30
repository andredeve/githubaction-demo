<form id="formPergunta" class="form-horizontal" method="POST" action="{$app_url}pergunta/{$acao}">
    <input type="hidden" name="id" value="{$pergunta->getId()}"/>
    <input type="hidden" name="ajax" value="true"/>
    <input type="hidden" name="acao" value="{$acao}"/>
    <input type="hidden" name="ordem" value="{$pergunta->getOrdem()}"/>
    <input type="hidden" name="setor_fase_id" value="{$pergunta->getSetorFase()->getId()}"/>
    <div class="form-group">
        <label class="col-form-label">Ativa?</label><br/>
        <div class="form-check form-check-inline">
            <label class="custom-control custom-radio">
                <input id="perguntaAtiva" name="isAtiva" value="1" type="radio" class="custom-control-input" {if $pergunta->getIsAtiva() eq true}checked{/if}>
                <label class="custom-control-label" for="perguntaAtiva">Sim</label>
            </label>
        </div>
        <div class="form-check form-check-inline">
            <label class="custom-control custom-radio">
                <input id="perguntaInativa" name="isAtiva" value="0" type="radio" class="custom-control-input" {if $pergunta->getIsAtiva() eq false}checked{/if}>
                <label class="custom-control-label" for="perguntaInativa">Não</label>
            </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label required">Pergunta:</label>
        <textarea class="form-control input-with-limit" maxlength="255" placeholder="Digite aqui sua pergunta" name="descricao" required="true" rows="5">{$pergunta->getDescricao()}</textarea>
        <span class="form-text text-muted caracteres-restantes"></span>
    </div>
    <div class="form-group">
        <label class="col-form-label">Orientação:</label>
        <textarea class="form-control editor" placeholder="Digite alguma orientação ou observação da pergunta que julgar necesssário." name="orientacao" rows="3">{$pergunta->getOrientacao()}</textarea>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</form>





