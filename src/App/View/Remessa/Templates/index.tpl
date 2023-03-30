<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="{$app_url}remessa"><i class="fa fa-share"></i> Histórico de
                    Remessas</a>
            </li>
           {* <li class="nav-item">
                <a class="nav-link" href="{$app_url}remessa/buscar"><i class="fa fa-file-o"></i> Gerar Nova</a>
            </li>*}
        </ul>
    </div>
    <div class="card-body">
        <div class="remessa-table">
            <table class="table table-sm table-bordered">
                <tr class="bg-light">
                    <td><i class="fa fa-filter"></i> Nº Remessa</td>
                    <td><i class="fa fa-filter"></i> Intervalo de Data Remessa</td>
                    <td><i class="fa fa-filter"></i> Setor de Origem</td>
                    <td><i class="fa fa-filter"></i> Responsável de Origem</td>
                    <td><i class="fa fa-filter"></i> Setor de Destino</td>
                    <td><i class="fa fa-filter"></i> Responsável de Destino</td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <input type="number" class="form-control form-control-sm numero_filter" name="numero_filter"/>
                    </td>
                    <td style="width: 23%">
                        <div class="row">
                            <div class="col">
                                <input data_fim_id="data_remessa_fim" placeholder="de" type="text"
                                       name="data_remessa_ini"
                                       id="data_remessa_ini" class="form-control form-control-sm date-range"/>
                            </div>
                            <div class="col">
                                <input type="text" name="data_remessa_fim" placeholder="até" id="data_remessa_fim"
                                       class="form-control form-control-sm"/>
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="setor_origem_filter"
                               class="form-control form-control-sm setor_origem_filter"/>
                    </td>
                    <td>
                        <input type="text" name="responsavel_origem_filter"
                               class="form-control form-control-sm responsavel_origem_filter"/>
                    </td>
                    <td>
                        <input type="text" name="setor_destino_filter"
                               class="form-control form-control-sm setor_destino_filter"/>
                    </td>
                    <td>
                        <input type="text" name="responsavel_destino_filter"
                               class="form-control form-control-sm responsavel_destino_filter"/>
                    </td>
                    <td class="text-center vertical-middle">
                        <button type="button" class="btn btn-xs btn-block btn-outline-info btn-limpar-filtros"><i
                                    class="fa fa-refresh"></i></button>
                    </td>
                </tr>
            </table>
            <hr/>
            <table id="tabelaRemessas" class="table table-bordered table-sm table-hover"
                   style="display: none;font-weight: lighter">
                <thead>
                <tr class="bg-light">
                    <th>Remessa</th>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Setor Origem</th>
                    <th>Responsável Origem</th>
                    <th>Setor Destino</th>
                    <th>Responsável Destino</th>
                </tr>
                </thead>
                <tbody>
                {*{foreach $remessas as $remessa}
                    <tr>
                        <td class="text-center">{$remessa->getId()}</td>
                        <td>{$remessa->getData()}</td>
                        <td>{$remessa->getHora()}</td>
                        <td>{$remessa->getSetorOrigem()}</td>
                        <td>{$remessa->getResponsavelOrigem()}</td>
                        <td>{$remessa->getSetorDestino()}</td>
                        <td>{$remessa->getResponsavelDestino()}</td>
                    </tr>
                {/foreach}*}
                </tbody>
            </table>
        </div>
    </div>
</div>
