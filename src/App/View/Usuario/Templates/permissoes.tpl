<div class="form-group row">
    <div class="col-md-2 col-xs-12">
        <label class="col-form-label required">Perfil:</label>
        <select class="form-control form-control-sm" name="tipo" required="true">
            {html_options options=$tipo_options selected=$usuario->getTipo()}
        </select>
    </div>
    <div class="col">
        <label class="col-form-label required">Grupo:</label>
        <select class="form-control form-control-sm select2" name="grupo_id" required="true">
            <option value="">Selecione</option>
            {foreach $grupos as $grupo}
                <option value="{$grupo->getId()}"
                        {if $usuario->getGrupo()->getId() eq $grupo->getId()}selected{/if}>{$grupo->getNome()}</option>
            {/foreach}
        </select>
    </div>
    <div class="col-md-3 col-xs-12">
        <label class="col-form-label">Acesso ativo?</label><br/>
        <div class="form-check form-check-inline">
            <div class="custom-control custom-radio">
                <input id="radioAtivo1" type="radio" name="ativo" value="1" class="custom-control-input"
                       {if $usuario->getAtivo() eq true}checked{/if}>
                <label class="custom-control-label" for="radioAtivo1">Sim</label>
            </div>
        </div>
        <div class="form-check form-check-inline">
            <div class="custom-control custom-radio">
                <input id="radioAtivo2" type="radio" name="ativo" value="0" class="custom-control-input"
                       {if $usuario->getAtivo() eq false}checked{/if}>
                <label class="custom-control-label" for="radioAtivo2">Não</label>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-form-label">Setores atribuídos:</label>
    <button type="button" title="Clique para selecionar todos os setores"
            onclick="$('#select_setores_usuario').find('option').attr('selected',true).trigger('change');"
            class="float-right btn btn-xs btn-success"><i
                class="fa fa-check-square-o"></i> Marcar Todos
    </button>
    <select id="select_setores_usuario" name="setores_id[]" class="select2Tree" multiple="true" required="true">
        {include file="../../Setor/Templates/select.tpl"}
    </select>
    <small class="form-text text-muted">*O usuário terá acesso aos processos dos setores escolhidos. Usuários master têm
        acesso a todos os setores.
    </small>
</div>
