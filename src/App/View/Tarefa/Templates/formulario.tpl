<form id="formTarefa" class="form-horizontal" method="POST" action="{$app_url}tarefa/{$acao}">
    <input type="hidden" name="id" value="{$tarefa->getId()}"/>
    <input type="hidden" name="ajax" value="true"/>
    <input type="hidden" name="acao" value="{$acao}"/>
    <input type="hidden" name="ordem" value="{$tarefa->getOrdem()}"/>
    <input type="hidden" name="setor_fase_id" value="{$tarefa->getSetorFase()->getId()}"/>
    <div class="form-group">
        <label class="col-form-label">Ativa?</label><br/>
        <div class="form-check form-check-inline">
            <div class="custom-control custom-radio">
                <input id="tarefaAtiva" name="isAtiva" value="1" type="radio" class="custom-control-input" {if $tarefa->getIsAtiva() eq true}checked{/if}>
                <label class="custom-control-label" for="tarefaAtiva">Sim</label>
            </div>
        </div>
        <div class="form-check form-check-inline">
            <div class="custom-control custom-radio">
                <input id="tarefaInativa" name="isAtiva" value="0" type="radio" class="custom-control-input" {if $tarefa->getIsAtiva() eq false}checked{/if}>
                <label class="custom-control-label" for="tarefaInativa">Não</label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label required">Tarefa:</label>
        <textarea class="form-control input-with-limit" maxlength="255" placeholder="Digite aqui sua tarefa" name="descricao" required="true" rows="5">{$tarefa->getDescricao()}</textarea>
        <span class="form-text text-muted caracteres-restantes"></span>
    </div>
    <div class="form-group">
        <label class="col-form-label">Orientação:</label>
        <textarea class="form-control" placeholder="Digite alguma orientação para execuçao da tarefa" name="orientacao" rows="3">{$tarefa->getOrientacao()}</textarea>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i class="fa fa-times"></i> Cancelar</a>
    </div>
</form>





