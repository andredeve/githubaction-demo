<div class="card">
    <div class="card-body">
        <form id="formProcessosForaFluxo" class="form-horizontal" method="POST">
            <div class="form-group row">
                <div class="col">
                    <label>Assunto:</label>
                    <div class="input-group">
                        <select name='assunto_id' class="select2">
                            <option value=''>Selecionar</option>
                            {foreach $assuntos as $assunto}
                                <option value="{$assunto->getId()}" {if $assunto->getId() eq $assunto_id}selected{/if}>{$assunto->getDescricao()}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="col">
                    <label>Interessado:</label>
                    <div class="input-group">
                        <select name='interessado_id' class="select_interessado">
                            <option value=""></option>
                            {if $interessado}
                                <option value="{$interessado->getId()}"
                                    selected>{$interessado}</option>
                            {/if}
                        </select>
                    </div>
                </div>
            </div>
            
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
                
            </div>
            <div class="form-group row">
                <div class="text-right offset-10 col-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fa fa-filter"></i> Filtrar</button>
                </div>   
            </div>            
        </form>
    </div>
</div>
<br/>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active"  data-toggle="tab" href="#processosTab" role="tab" aria-controls="processosTab">{$nomenclatura}s</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#porInteressadoTab" role="tab" aria-controls="porInteressadoTab">Por Interessado</a>
            </li>
        </ul>
    </div>
    <div class="card-body tab-content">
        <div class="tab-pane fade show active" id="processosTab" role="tabpanel">
            <table id="tabelaProcessoForaFluxograma" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Protocolo</th>
                        <th>Assunto</th>
                        <th>Interessado</th>
                        <th>Data Abertura</th>
                        <th>Objeto</th>
                        <th>Quantidade de Correções</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $processos as $processo}
                        <tr id='visualizar:{$processo[0]->getId()}'>
                            <td>{$processo[0]}</td>
                            <td>{$processo[0]->getAssunto(true)}</td>
                            <td>{$processo[0]->getInteressado()}</td>
                            <td>{$processo[0]->getDataAbertura(true)}</td>
                            <td>{$processo[0]->getObjeto()}</td>
                            <td>{$processo['qtde_fora_fluxo']}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>            
        </div>
        <div class="tab-pane fade" id="porInteressadoTab" role="tabpanel">
            <table id="tabelaProcessoForaFluxogramaPorInteressado" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Interessado</th>
                        <th class="text-center align-middle">Total Geral</th>
                        <th class="text-center">0 Devoluções</th>
                        <th class="text-center">1 Devolução</th>
                        <th class="text-center">2 Devoluções</th>
                        <th class="text-center">3 Devoluções</th>
                        <th class="text-center">Mais de 3 Devoluções</th>
                        <th class="text-center">Total Corrigidos</th>
                        <th class="text-center">Taxa de Aproveitamento</th>
                    </tr>
                                        
                </thead>
                <tbody>
                    {foreach $processosPorInteressado as $p}
                        <tr>
                            <td>{$p["interessado"]}</td>
                            <td class="text-center">{$p["qtde_total"]}</td>
                            <td class="text-center">{$p["qtde_devolvido_0"]}</td>
                            <td class="text-center">{$p["qtde_devolvido_1"]}</td>
                            <td class="text-center">{$p["qtde_devolvido_2"]}</td>
                            <td class="text-center">{$p["qtde_devolvido_3"]}</td>
                            <td class="text-center">{$p["qtde_devolvido_mais"]}</td>
                            <td class="text-center">{$p['qtde_total_devolvido']}</td>
                            <td class="text-center">{(($p["qtde_total"] - $p['qtde_total_devolvido']) * 100)/$p["qtde_total"]}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>        
    </div>
</div>

<script defer type="text/javascript" src="{$app_url}min/g=datatableButtonsJs"></script>
<script defer type="text/javascript" src="{$app_url}assets/js/view/relatorio/foraFluxograma.js"></script>