<div class="accordion" id="accordionGraficos">
    <div class="card">
        <div class="card-header p-2" id="headingAnaliseGrafica">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseAnaliseGrafica"
                        aria-expanded="true" aria-controls="collapseAnaliseGrafica">
                    <i class="fa fa-pie-chart"></i> Análise Gráfica
                </button>
            </h2>
        </div>
        <div id="collapseAnaliseGrafica" class="collapse" aria-labelledby="headingAnaliseGrafica"
             data-parent="#accordionGraficos">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div id="pieVencidosSetor"></div>
                    </div>
                    <div class="col">
                        <div id="pieVencidosAssunto"></div>
                    </div>
                    <div class="col">
                        <div id="pieVencidosInteressado"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="relatorioVencimentosForm" class="mb-2">
    <div class="card p-2">
        <div class="form-group row">
            <div class="col-3 hidden">
                <label>Vencimento em:</label>
                <select name="periodo_vencimento" id="periodo_vencimento" class="form-control form-control-sm">
                    <option value="">Selecione</option>
                    <option value="">Próximos 7 dias</option>
                    <option value="">Próximos 15 dias</option>
                    <option value="">Próximos 30 dias</option>
                </select>
            </div>
            <div class="col">
                <label>Vencimento {$parametros['nomenclatura']} (de):</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i
                                    class="fa fa-calendar"></i></span></div>
                    <input type="text" data_fim_id="data_vencimento_fim" id="data_vencimento_ini"
                           name="data_vencimento_ini"
                           class="form-control form-control-sm date-range"/>
                </div>
            </div>
            <div class="col">
                <label>Vencimento {$parametros['nomenclatura']} (até):</label>
                <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i
                                    class="fa fa-calendar"></i></span></div>
                    <input type="text" id="data_vencimento_fim" name="data_vencimento_fim"
                           class="form-control form-control-sm"/>
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
                    <option value="{$usuario->getId()}">{if $usuario->getPessoa()}{$usuario->getPessoa()->getNome()}{else} Não Informado Cód. {$usuario->getId()}{/if}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</form>
<table id="tabelaControleVencimentos" class="table table-bordered table-hover table-sm">
    <thead class="bg-light">
    <tr>
        <th class="text-center">Processo</th>
        <th>Interessado</th>
        <th>Assunto</th>
        <th>Setor Atual</th>
        <th class="text-center">Vencimento</th>
        <th>Responsável</th>
        <th class="text-center">Vence em (dias)</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    {foreach $tramites_vencidos as $tramite}
        {$processo = $tramite->getProcesso()}
        {$data_vencimento=$tramite->getDataVencimento()}
        <tr id="visualizar:{$processo->getId()}" title="{$tramite->getParecer()}">
            <td class="text-center">{$processo}</td>
            <td>{$processo->getInteressado()}</td>
            <td>{$processo->getAssunto()}</td>
            <td>{$tramite->getSetorAtual()->getNome()}</td>
            <td class="text-center">{$data_vencimento->format('d/m/Y')}</td>
            <td>
                {if $tramite->getResponsavel()}
                    {$tramite->getResponsavel()}
                {/if}
            </td>
            <td class="text-center">
                {$tramite->getDiasVencidos($data_vencimento)}
            </td>
            <td class="col-actions">
                <a title="Notificar" href="{$app_url}tramite/notificar/{$tramite->getId()}"
                   class="btn btn-danger btn-xs"><i class="fa fa-envelope-open-o"></i></a>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
