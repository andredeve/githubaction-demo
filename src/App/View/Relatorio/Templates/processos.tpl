<div class="card">
    <div class="card-body">
        <form id="formProcessosPeriodo" class="form-horizontal" method="POST">
            <div class="form-group row">
                <div class="col">
                    <label>Período (de):</label>
                    <div class="input-group">
                        <input id="data_periodo_ini" data_fim_id="data_periodo_fim" value="{$dataInicio}"  type="text" name="periodo_ini" class="form-control form-control-sm date-range form-filter"/>
                        <div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    </div>
                </div>
                <div class="col">
                    <label>Período(até):</label>
                    <div class="input-group">
                        <input id="data_periodo_fim" type="text" name="periodo_fim" value="{$dataFim}" class="form-control form-control-sm form-filter"/>
                        <div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    </div>
                </div>
                <div class="col">
                    <label>Qtde. Registros:</label>
                    <input id="qtde_registros" class="form-control form-control-sm form-filter" type="number" name="qtde_registros" value="{$limite}"/>
                </div>
                <div class="col-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fa fa-filter"></i> Filtrar</button>
                    {*<a href="{$app_url}relatorio/imprimirProcessos" class="btn btn-success"><i class="fa fa-print"></i> Imprimir</a>*}
                </div>
            </div>
        </form>
    </div>
</div>
<br/>
<div class="row">
    <div class="col">
        <div id="piePorOrigemProcesso"></div>
    </div>
    <div class="col">
        <div id="piePorAssuntoProcesso"></div>
    </div>
    <div class="col">
        <div id="piePorResponsavelProcesso"></div>
    </div>
</div>
{function tabelaTotal level=0}
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>{$nome}</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>
            {$total=0}
            {foreach $registros as $registro}
                <tr>
                    <td>{$registro['processo']->$metodo(true)}</td>
                    <td>{$registro['qtde']}</td>
                </tr>
                {$total=$total+$registro['qtde']}
            {/foreach}
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th>{$total}</th>
            </tr>
        </tfoot>
    </table>
{/function}
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active"  data-toggle="tab" href="#porOrigemTab" role="tab" aria-controls="porOrigemTab">Por Origem</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#porAssuntoTab" role="tab" aria-controls="porAssuntoTab">Por Assunto</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#porResponsavelTab" role="tab" aria-controls="porResponsavelTab">Por Usuário (abertura)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#listaProcessosTab" role="tab" aria-controls="listaProcessosTab">Processos</a>
            </li>
        </ul>
    </div>
    <div class="card-body tab-content">
        <div class="tab-pane fade show active" id="porOrigemTab" role="tabpanel">
            {tabelaTotal nome="Origem" metodo="getOrigem" registros=$processosPorOrigem}
        </div>
        <div class="tab-pane fade" id="porAssuntoTab" role="tabpanel">
            {tabelaTotal nome="Assunto" metodo="getAssunto" registros=$processosPorAssunto}
        </div>
        <div class="tab-pane fade" id="porResponsavelTab" role="tabpanel">
            {tabelaTotal nome="Responsavel" metodo="getUsuarioAbertura" registros=$processosPorResponsavel}
        </div>
        <div class="tab-pane fade" id="listaProcessosTab" role="tabpanel">
            
            <table id="tabelaProcessoPorPeriodo" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Protocolo</th>
                        <th>Assunto</th>
                        <th>Interessado</th>
                        <th>Setor Origem</th>
                        <th>Setor Atual</th>
                        <th>Data Cadastro</th>
                        <th>Objeto</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $processos as $processo}
                        <tr>
                            <td>{$processo}</td>
                            <td>{$processo->getAssunto(true)}</td>
                            <td>{$processo->getInteressado()}</td>
                            <td>{$processo->getSetorOrigem()}</td>
                            <td>{$processo->getSetorAtual()}</td>
                            <td>{$processo->getDataAbertura(true)}</td>
                            <td>{$processo->getObjeto()}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            
        </div>
    </div>
</div>

<script defer type="text/javascript" src="{$app_url}min/g=datatableButtonsJs"></script>