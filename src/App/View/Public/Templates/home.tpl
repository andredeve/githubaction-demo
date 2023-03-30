<p class="lead" xmlns="http://www.w3.org/1999/html">Seja bem-vindo(a), {$usuario_logado->getPessoa()->getPrimeiroNome()}.</p>
{if $usuario_logado->getTipo() == App\Enum\TipoUsuario::INTERESSADO}
    <p style="font-size: 1.3em">Aqui neste portal você poderá fazer e acompanhar suas solicitações junto aos órgãos competentes.
        <br/>Fique atento ao seu e-mail, pois é por ele que te avisaremos sobre toda movimentação de sua solicitação de abertura de protocolo.</p>
    {include file="../../Contribuinte/Templates/nav.tpl"}
    <div class="processo-table">
        {include file="../../Contribuinte/Templates/filtros.tpl"}
        <table id="tabelaProcessosContribuintes"
               class="table table-bordered table-hover table-sm tabelaProcessosContribuintes text-center"
               hide_checkbox=""
               verificar_vencimento="false"
               processoExterno="true"
               url="{$app_url}src/App/Ajax/Contribuinte/listar_server_side.php">
            <thead class="bg-light">
            <tr>
                <th></th>
                <th>{$parametros['nomenclatura']}</th>
                <th>Assunto</th>
                <th class="text-center">Data Abertura</th>
                <th class="text-center">Status</th>
                <th class="text-center">Setor Atual</th>
                <th></th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
{else}
    <div class="row">
        <div class="col-md col-sm-12">
            <div class="small-box bg-yellow-gradient">
                <a href="{$app_url}processo/arquivados" class="text-white">
                    <div class="inner">
                        <h3 class="qtde_processos_receber pl-2">{$qtde_receber}</h3>
                        <p class="pl-2">A Receber</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                </a>
                <a href="{$app_url}processo/arquivados" class="small-box-footer">Mais informações
                    <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md col-sm-12">
            <div class="small-box bg-light-blue-gradient">
                <a href="{$app_url}processo/abertos" class="text-white">
                    <div class="inner">
                        <h3 class="qtde_processos_abertos pl-2">{$qtde_aberto}</h3>
                        <p class="pl-2">Em Aberto</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-send"></i>
                    </div>
                </a>
                <a href="{$app_url}processo/abertos" class="small-box-footer">
                    Mais informações
                    <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md col-sm-12">
            <div class="small-box bg-red-gradient">
                <a href="#" class="text-white btn-ver-vencidos">
                    <div class="inner">
                        <h3 class="qtde_processos_vencidos pl-2">{$qtde_vencidos}</h3>
                        <p class="pl-2">Trâmites Vencidos</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                </a>
                <a href="#" class="small-box-footer btn-ver-vencidos">
                    Mais informações
                    <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-md col-sm-12">
            <div class="small-box bg-red-gradient">
                <a href="#" class="text-white btn-ver-processos-vencidos">
                    <div class="inner">
                        <h3 class="qtde_processos_vencidos pl-2">{$qtde_processos_vencidos}</h3>
                        <p class="pl-2">Processos Vencidos</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                </a>
                <a href="#" class="small-box-footer btn-ver-processos-vencidos">
                    Mais informações
                    <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        {if $usuario_signatario}
            <div class="col-md col-sm-12">
                <div class="small-box bg-success text-white">
                    <a href="{$app_url}assinatura/emProcesso" class="text-white">
                        <div class="inner">
                            <h3 class="qtde_processos_arquivados pl-2">{$qtd_requisicao_assinatura}</h3>
                            <p class="pl-2">{$texto_qtd_requisicao_assinatura}</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-folder-open-o"></i>
                        </div>
                    </a>
                    <a href="{$app_url}assinatura/emProcesso" class="small-box-footer">
                        Mais informações
                        <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        {/if}
        {if $contribuinteHabilitado}
            <div class="col-md col-xs-12">
                <div class="small-box bg-yellow-gradient">
                    <a href="{$app_url}processo/contribuintes" class="text-white">
                        <div class="inner">
                            <h3 class="qtde_processos_contribuintes pl-2">{$qtde_contribuintes}</h3>
                            <p class="pl-2">Contribuintes (Recepção)</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                    </a>
                    <a href="{$app_url}processo/contribuintes" class="small-box-footer">
                        Mais informações
                        <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        {/if}
    </div>
    {if $usuario_logado->getTipo() neq \App\Enum\TipoUsuario::USUARIO && $usuario_logado->getTipo() neq \App\Enum\TipoUsuario::VISITANTE}
        <div class="row">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <label class="col-md-2">Assunto:</label>
                            <div class="col">
                                <select id="select_asssunto_mensal_filter" name="assunto_filter"
                                        class="form-control filter-mensal-processos select_assunto">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <label class="col-md-2 text-right">Ano:</label>
                            <div class="col-md-2">
                                <select id="select_ano_mensal_filter" name="ano_filter"
                                        class="form-control select2 filter-mensal-processos">
                                    <option value="">Todos</option>
                                    {foreach $anos as $ano}
                                        <option value="{$ano}" {if $ano eq $exercicio_atual}selected{/if}>{$ano}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="graficoMensal"></div>
                </div>
            </div>
            <div class="col-md-5 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col"><strong>Últimas movimentações</strong></div>
                            <div class="col-md-6 pull-right">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class='fa fa-calendar'></i>
                                        </div>
                                    </div>
                                    <input id="data_movimentacao" type="text" name="data_mov" value=""
                                           class="datepicker form-control form-control-sm data"/>
                                    <div class="input-group-append">
                                        <div class="input-group-text"><i class='fa fa-search'></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="max-height: 400px;overflow-y: auto">
                        <ul class="list-group">
                            {foreach $movimentacoes as $movimentacao}
                                <li class="list-group-item">
                                    <i class="fa fa-check-square-o fa-lg text-primary"></i> {$movimentacao->getMensagem()}
                                    <br/>
                                    <small class="text-muted"><i
                                                class="fa fa-clock-o"></i> {$movimentacao->getHorario()->format('d/m/Y')}
                                        às {$movimentacao->getHorario()->format('H:i')}</small>
                                    |
                                    <small class="text-muted"><i class="fa fa-user-o"></i> {$movimentacao->getUsuario()}
                                    </small>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <label class="col-md-1">Assunto:</label>
                    <div class="col">
                        <select id="select_asssunto_filter" name="assunto_filter"
                                class="form-control select_assunto filter-pizza-processos">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <label class="col-md-2 text-right">Interessado:</label>
                    <div class="col">
                        <select name="interessado_filter"
                                class="form-control select2 filter-pizza-processos select_interessado">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <label class="col-md-2 text-right">Responsável:</label>
                    <div class="col">
                        <select id="select_responsavel_filter" name="responsavel_filter"
                                class="form-control select2 filter-pizza-processos">
                            <option value="">Todos</option>
                            {foreach $usuarios as $usuario}
                                <option value="{$usuario->getId()}">{$usuario}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div id="pieReceber"></div>
                </div>
                <div class="col">
                    <div id="pieAberto"></div>
                </div>
                <div class="col">
                    <div id="pieVencidos"></div>
                </div>
            </div>
        </div>
    {/if}
    <div class="mt-2" style="max-height: 250px;overflow-y: auto">
        <table class="table table-bordered table-sm table-hover text-center tabelaVencimentosProximo">
            <thead>
            <tr class="table-warning">
                <th colspan="8" class="text-center lead">{$parametros['nomenclatura']}s com vencimento próximo:</th>
            </tr>
            <tr>
                <th class="hidden"></th>
                <th>{$parametros['nomenclatura']}</th>
                <th class="text-left">Assunto</th>
                <th class="text-left">Interessado</th>
                <th class="text-left">Setor Atual</th>
                <th>Abertura</th>
                <th>Vencimento</th>
                <th>Dias Vencimento</th>
            </tr>
            </thead>
            <tbody>
            {foreach $processos_vencimento_proximo as $processo}
                <tr id="visualizar:{$processo->getId()}" title="{$processo->getObjeto()}">
                    <td class="hidden">{$processo->getId()}</td>
                    <td>{$processo}</td>
                    <td class="text-left">{$processo->getAssunto()}</td>
                    <td class="text-left">{$processo->getInteressado()}</td>
                    <td class="text-left">{$processo->getSetorAtual()}</td>
                    <td>{$processo->getDataAbertura(true)}</td>
                    <td>{$processo->getDataVencimento(true)}</td>
                    <td>{$processo->getDiasVencimento()}</td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="8" class="text-muted text-left">Nenhum {$parametros['nomenclatura']} com vencimento
                        próximo.
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <div class="mt-2" style="max-height: 250px;overflow-y: auto">
        <table class="table table-bordered table-sm table-hover text-center tabelaVencimentosProximo">
            <thead>
            <tr class="table-warning">
                <th colspan="8" class="text-center lead">Documentos com vencimento próximo:</th>
            </tr>
            <tr>
                <th class="hidden"></th>
                <th>{$parametros['nomenclatura']}</th>
                <th class="text-left">Categoria</th>
                <th class="text-left">Número</th>
                <th class="text-left">Exercício</th>
                <th>Data</th>
                <th>Vencimento</th>
                <th>Dias Vencimento</th>
            </tr>
            </thead>
            <tbody>
            {foreach $documentos_vencimento_proximo as $documento}
                {$processo=$documento->getProcesso()}
                <tr id="visualizar:{$processo->getId()}" title="{$documento->getObservacoes()}">
                    <td class="hidden">{$processo->getId()}</td>
                    <td>{$processo}</td>
                    <td class="text-left">{$documento->getCategoria()}</td>
                    <td class="text-left">{$documento->getNumero()}</td>
                    <td class="text-left">{$documento->getExercicio()}</td>
                    <td>{$documento->getData(true)}</td>
                    <td>{$documento->getVencimento(true)}</td>
                    <td>{$documento->getDiasVencimento()}</td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="8" class="text-muted text-left">Nenhum documento com vencimento próximo.</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
{/if}
