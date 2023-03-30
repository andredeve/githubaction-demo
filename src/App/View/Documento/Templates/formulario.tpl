<form id="formDocumento" class="form-horizontal" method="POST" action="{$app_url}documento/{$acao}">
    <input type="hidden" name="id" value="{$documento->getId()}"/>
    <input type="hidden" name="indice" value="{$indice}"/>
    <input type="hidden" name="ajax" value="true"/>
    <input type="hidden" name="acao" value="{$acao}"/>
    <input type="hidden" name="processo_id" value="{$documento->getProcesso()->getId()}"/>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label">Categoria:</label>
            <a id="cadastrar:CategoriaDocumento" href_select="select_categoria" href="#"
               class="btn btn-xs btn-success float-right btn-cadastrar-modal"><i class="fa fa-plus"></i></a>
            <select id="select_categoria" class="form-control form-control-sm select2" name="categoria_id" required>
                <option value=""></option>
                {foreach $categorias as $categoria}
                    <option value="{$categoria->getId()}"
                            {if $categoria->getId() eq $documento->getCategoria()->getId()}selected{/if}>{$categoria}</option>
                {/foreach}
            </select>
        </div>
        <div class="col-3">
            <label class="col-form-label">Número:</label>
            <input type="number" name="numero" value="{$documento->getNumero()}" class="form-control-sm form-control"
                   required/>
        </div>
        <div class="col-3">
            <label class="col-form-label">Exercício:</label>
            <input type="number" name="exercicio" value="{$documento->getExercicio()}"
                   class="form-control-sm form-control" required/>
        </div>
    </div>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label">Data:</label>
            <input type="text" name="data" value="{$documento->getData(true)}"
                   class="form-control-sm form-control datepicker" required/>
        </div>
        <div class="col">
            <label class="col-form-label">Vencimento:</label>
            <input type="text" name="vencimento" value="{$documento->getVencimento(true)}"
                   class="form-control-sm form-control datepicker" required/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label">Observações:</label>
        <textarea class="form-control" name="observacoes">{$documento->getObservacoes()}</textarea>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i>
            Salvar
        </button>
        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                    class="fa fa-times"></i> Cancelar</a>
    </div>
</form>





