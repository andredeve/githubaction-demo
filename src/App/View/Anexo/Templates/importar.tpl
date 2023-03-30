<form id="formAnexoImportarPesquisa" class="form-horizontal" enctype="multipart/form-data" method="POST"
      action="{$config["lxfiorilli"]}index.php?entidade=index&method=pesquisar">
    <div class="form-group row">
        <div class="col-3">
            <label class="col-form-label"> Banco: </label>
            <select class="form-control" name="bancoAno">
                {foreach $bancos as $banco}
                    <option value="{$banco}">{$banco}</option>
                {/foreach}
            </select>
        </div>
        <div class="col">
            <label class="col-form-label"> Documento: </label>
            <select class="form-control" name="documento">
                <option value="empenho">Empenho</option>
                <option value="ordemPagamento">Ordem Pagamento</option>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label"> Número (de): </label>
            <input type="number" class="form-control form-control-sm " name="numero_ini"  
                   value="" />
        </div>
        <div class="col">

            <label class="col-form-label"> Número (até): </label>
            <input type="number" class="form-control form-control-sm " name="numero_fim"  
                   value="" />
        </div>
        <div class="col">

            <label class="col-form-label"> Exercício: </label>
            <input type="text" minlength="4" class="form-control ano form-control-sm " name="exercicio"  
                   value="" />
        </div>
        <div class="col">

            <label class="col-form-label"> Proc. Licitação: </label>
            <input type="text" class="form-control ano form-control-sm " name="processoLicitacao"  
                   value="" />
        </div>
    </div>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label"> Fornecedor</label>
            <select class="form-control select-fornecedor-importacao" url_pesquisa="{$config["lxfiorilli"]}" name="fornecedor">

            </select>
        </div>
    </div>


    {*    <hr/>*}
    <div class="form-group text-right">
        <button type="submit" data-style="expand-right" class="btn btn-warning ladda-button"><i class="fas fa-search"></i>
            Pesquisar
        </button>
        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                class="fa fa-times"></i> Cancelar</a>
    </div>
</form >
<br>
<hr>
<form id="formAnexoImportar" method="POST" class="form" action="{$app_url}Anexo/importar">
    <input type="hidden" name="processo_id" value="{$processo->getId()}" />
    {foreach $processo->getAnexos() as $anexo}
        {if $anexo->getCodigoImportacao() != null}
            <input type="hidden" name="codigoImportacao[]" value="{$anexo->getCodigoImportacao()}">   
        {/if}
    {/foreach}


    <table id="tabelaAnexoImportacao" class="table table-bordered ">
        <thead>
            <tr>
                <th class='text-center'>#</th>
                <th class='text-center'>Número</th>
                <th class='text-center'>Unid. Orçamentaria/Unid. Gestorar</th>
                <th class='text-center'>Fornecedor</th>
                <th class='text-center'>Valor</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

    <br>
    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary ">
            <i class="fa fa-save"></i> Importar
        </button>
    </div>

</form>   
<hr>