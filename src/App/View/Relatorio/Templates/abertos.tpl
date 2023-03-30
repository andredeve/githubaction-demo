<form id="relatorioAbertosForm" class="form-horizontal">
    <div class="card p-2">
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
            <div class="col">
                <label>Data {$parametros['nomenclatura']} (de):</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i
                                    class="fa fa-calendar"></i></span></div>
                    <input type="text" data_fim_id="data_processo_fim" id="data_processo_ini"
                           class="form-control form-control-sm date-range"/>
                </div>
            </div>
            <div class="col">
                <label>Data {$parametros['nomenclatura']} (até):</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i
                                    class="fa fa-calendar"></i></span></div>
                    <input type="text" id="data_processo_fim" class="form-control form-control-sm"/>
                </div>
            </div>
            <div class="col">
                <label>Data Trâmite (de):</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i
                                    class="fa fa-calendar"></i></span></div>
                    <input type="text" data_fim_id="data_tramite_fim" id="data_tramite_ini"
                           class="form-control form-control-sm date-range"/>
                </div>
            </div>
            <div class="col">
                <label>Data Trâmite (até):</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i
                                    class="fa fa-calendar"></i></span></div>
                    <input type="text" id="data_tramite_fim" class="form-control form-control-sm"/>
                </div>
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
                <div class="float-right">
                    <a href="#" entidade="Assunto"
                       title="Pesquisa avançada por Interessado"
                       class="btn btn-xs btn-info btn-selectionar-entidade"><i
                                class="fa fa-search"></i></a>
                </div>
                <select name="assunto_id" class="form-control assunto_filter select_assunto">
                    <option value=""></option>
                </select>
            </div>
            <div class="col">
                <label>Interessado:</label>
                <a href="#"
                   title="Pesquisa avançada por Interessado"
                   class="btn btn-xs btn-info btn-pesquisar-interessado pull-right"><i
                            class="fa fa-search"></i></a>
                <select id="" name="interessado_id" class="form-control interessado_filter select_interessado">
                    <option value=""></option>
                </select>
            </div>
            <div class="col">
                <label>Responsável:</label>
                <select name="responsavel_id" data-allow-clear="true" class="form-control select2 responsavel_filter">
                    <option value=""></option>
                    {foreach $usuarios as $usuario}
                        <option value="{$usuario->getId()}">{$usuario}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline1" value="setor" name="agrupar"
                           class="custom-control-input agrupar_filter">
                    <label class="custom-control-label" for="customRadioInline1">Agrupar por Setor</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline2" value="responsavel" name="agrupar"
                           class="custom-control-input agrupar_filter">
                    <label class="custom-control-label" for="customRadioInline2">Agrupa por Responsável</label>
                </div>
            </div>
            <div class="col text-right">
                {*<button class="btn btn-primary" type="submit">Gerar</button>*}
                <button class="btn btn-light border btn-limpar-filtros-relatorio" type="reset"><i class="fa fa-refresh"></i>
                    Limpar
                </button>
            </div>
        </div>
    </div>
</form>
<br/>
{include file="../../Processo/Templates/listar_relatorio.tpl"}
<script defer type="text/javascript" src="{$app_url}min/g=datatableButtonsJs"></script>
