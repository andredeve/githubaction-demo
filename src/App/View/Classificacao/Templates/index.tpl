{*<a title="Cadastre um nova Classificação de Documento" class="btn btn-primary btn-loading"
   href="{$app_url}classificacao/cadastrar"><i class="fa fa-plus"></i> Nova</a>
<a title="Imprimi lista de classificações" target="_blank" class="btn btn-warning disabled"
   href="{$app_url}classificacao/imprimir"><i class="fa fa-print"></i> Imprimir</a>
<hr/>*}
{* Função que lista recursivamente os componentes do ato*}
{function name=listar_classificacoes level=0}
    {foreach $data as $classificacao}
        <tr id="tabela{$level}">
            <td><span class="badge badge-info">{$classificacao->getCodigo()}</span></td>
            <td>{$classificacao->getTitulo()}</td>
            <td>{$classificacao->getFaseCorrente()}</td>
            <td>{$classificacao->getFaseIntermediaria()}</td>
            <td>{$classificacao->getDestinacaoFinal(true)}</td>
            <td>{$classificacao->getObservacoes()}</td>
            {*<td class="text-center">
                <div class="btn-group">
                    <a class="btn btn-info btn-xs btn-loading" title="Editar"
                       href="{$app_url}classificacao/editar/id/{$classificacao->getId()}"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-danger btn-xs btn-excluir" title="Excluir"
                       href="{$app_url}classificacao/excluir/id/{$classificacao->getId()}"><i
                                class="fa fa-times"></i></a>
                </div>
            </td>*}
        </tr>
        {$qtde=$classificacao->getClassificacoes()->count()}
        {if $qtde gt 0}
            {listar_classificacoes data=$classificacao->getClassificacoes() level=$level+1}
        {/if}
    {/foreach}
{/function}
<table id="tabelaClassificacoes" class="table table-bordered table-hover  table-sm">
    <thead class="bg-light">
    <tr>
        <th rowspan="2">#</th>
        <th rowspan="2">Título</th>
        <th colspan="2" class="text-center">Prazos de Guarda</th>
        <th rowspan="2">Destinação Final</th>
        <th rowspan="2">Observações</th>
        {*<th rowspan="2"></th>*}
    </tr>
    <tr>
        <th>Fase Corrente</th>
        <th>Fase Intermediária</th>
    </tr>
    </thead>
    <tbody>
    {listar_classificacoes data=$classificacoes}
    </tbody>
</table>
    <script defer type="text/javascript" src="{$app_url}min/g=datatableButtonsJs"></script>
<script defer="true" src="{$app_url}assets/js/view/classificacao/index.js?v={$file_version}"></script>
