<form id="relatorioArquivadosForm" class="form-horizontal">
    <div class="form-group row">
        {*<div class="col">
            <label>Exercício:</label>
            <select name="exercicio" class="form-control select2">
                <option value="">Todos</option>
                {foreach $exercicios as $exercicio}
                    <option value="{$exercicio}">{$exercicio}</option>
                {/foreach}
            </select>
        </div>*}
        <div class="col-3">
            <label>Data Arquivamento (de):</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                <input type="text" data_fim_id="data_arquivamento_fim" id="data_arquivamento_ini" class="form-control form-control-sm date-range"/>
            </div>
        </div>
        <div class="col-3">
            <label>Data Arquivamento (até):</label>
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                <input type="text" id="data_arquivamento_fim" class="form-control form-control-sm"/>
            </div>
        </div>
        <div class="col">
            <label>Responsável Arquivamento:</label>
            <select name="responsavel_id" class="form-control select2 responsavel_filter">
                <option value=""></option>
                {foreach $usuarios as $usuario}
                    <option value="{$usuario->getId()}">{$usuario}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="form-group row">
        <div class="col">
            <label>Setor Atual:</label>
            <select name="setor_atual_id" class="form-control select2 setor_atual_filter">
                <option value=""></option>
                {foreach $setores as $setor}
                    <option value="{$setor->getId()}">{$setor}</option>
                {/foreach}
            </select>
        </div>
        <div class="col">
            <label>Assunto:</label>
            <select name="assunto_id" class="form-control select_assunto assunto_filter">
                <option value=""></option>
                {*{foreach $assuntos as $assunto}
                    <option value="{$assunto->getId()}">{$assunto}</option>
                {/foreach}*}
            </select>
        </div>
        <div class="col">
            <label>Interessado:</label>
            <select id="" name="interessado_id" class="form-control select_interessado">
                <option value=""></option>
            </select>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <button class="btn btn-light border btn-limpar-filtros-relatorio" type="reset"><i class="fa fa-refresh"></i>
            Limpar
        </button>
    </div>
</form>
<br/>
{include file="../../Processo/Templates/listar_relatorio.tpl" arquivados=1}
<script defer="" type="text/javascript" src="{$app_url}min/g=datatableButtonsJs"></script>
