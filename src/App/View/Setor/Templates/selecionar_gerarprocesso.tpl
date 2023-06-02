<form id="formSelecionaSetor">
    <input type="hidden" name="id" id="id" value="{$processo}">
    <p class="text-info lead">Para gerar o {$nomenclatura} é necessário vinculá-lo à um dos teus setores:</p>
    <div class="form-group">
        <select class="form-control form-control-sm is-valid" name="setores_id">
            {foreach $setores as $setor}
                <option value="{$setor->getId()}">{$setor->getNome()}</option>
            {/foreach}
        </select>
    </div>
    <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-check"></i> OK</button>
    <button type="button" class="btn btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
            class="fa fa-times"></i> Cancelar
    </button>
</form>