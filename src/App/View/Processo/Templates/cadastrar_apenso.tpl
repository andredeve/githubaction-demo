<form id="processoApensoForm" class="form-horizontal" method="POST" enctype="multipart/form-data"
      action="{$app_url}Processo/inserirApenso">
    <input type="hidden" name="processo_pai_id" value="{$processo->getId()}"/>
    {include file="../../Processo/Templates/campos.tpl"}
    <hr/>
    <div class="form-group">
        <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
        <button type="button" class="btn btn-light border" onclick="$(this).closest('.modal').modal('hide');"> <i class="fa fa-times"></i> Cancelar</button>
    </div>
</form>