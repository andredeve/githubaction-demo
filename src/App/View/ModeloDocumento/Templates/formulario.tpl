{**********************************}
{***Última Alteração: 03/02/2023***}
{*************André****************}
{**********************************}
<form id="formModeloDocumento" method="POST" class="form-horizontal form-validate-ajax-file"
      enctype="multipart/form-data"
      action="{$app_url}modeloDocumento/{$acao}">
    <input type="hidden" name="id" value="{$modelo->getId()}"/>
    <input type="hidden" name="ajax" value="1"/>
    <input type="hidden" name="entidade" value="ModeloDocumento"/>
    {include file="../../Public/Templates/progress_bar.tpl"}
    <div class="form-group">
        <label class="col-form-label">Nome:</label>
        <input type="text" name="nome" class="form-control form-control-sm" value="{$modelo->getNome()}"
               required=""/>
    </div>
    <div class="form-group">
        <!-- TinyMCE -->
        <textarea id="editor_estrutura" class="form-control editor" height="600" name="texto">                  
          {$modelo->getTexto()}        
        </textarea>
    </div>
    <hr/>
    <div class="form-group"> 
        <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
        {if $modelo->getId() neq ""}
            <a class="btn btn-danger btn-excluir" title="Excluir"
               href="{$app_url}modeloDocumento/excluir/id/{$modelo->getId()}"><i class="fa fa-times"></i>
                Excluir</a>
        {/if}
        <a class="btn btn-light border btn-loading" href="{$app_url}modeloDocumento"><i class="fa fa-times"></i>
            Cancelar</a>
    </div>
</form>
<script defer type="text/javascript" src="{$app_url}assets/js/view/modeloDocumento/initUpload.js"></script>