<a class="btn btn-primary" href="{$app_url}localizacaoFisica/cadastrar"><i class="fa fa-plus"></i> Registrar</a>
<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#pesquisarLocalizacaoFisica"><i
            class="fa fa-search"></i> Pesquisar
</button>
<hr/>
<table id="tabelaLocalizacaoFisica" class="table table-bordered table-sm table-striped" style="display: none;">
    <thead>
    <tr>
        <th class="hidden"></th>
        <th colspan="3" class="text-center">Documento</th>
        <th colspan="5" class="text-center">Localização Física</th>
        <th></th>
        <th class="hidden" colspan="8"></th>
    </tr>
    <tr class="bg-light">
        <th>Código</th>
        <th>Número</th>
        <th>Exercício</th>
        <th>Data</th>
        <th>Local</th>
        <th>Tipo</th>
        <th>SubTipo</th>
        <th>Cadastro</th>
        <th>Última Alteração</th>
        <th></th>
        <th>Ref Local</th>
        <th>Ref Tipo Local</th>
        <th>Ref SubTipo Local</th>
        <th>Usuário Cadastro</th>
        <th>Usuário Alteração</th>
        <th>Ementa</th>
        <th>Observação</th>
        <th>Processo</th>
    </tr>
    </thead>
    <tbody>
    <!-- AJAX SOURCE -->
    </tbody>
</table>
{include file="../../LocalizacaoFisica/Templates/pesquisar.tpl"}