<div class="text-info mb-2">
    <strong>Recomendação:</strong> para aumentar a velocidade da sua busca, informe o máximo de parâmetros possível.
</div>
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#pesquisaProcessosTab" role="tab"
                   aria-controls="pesquisaProcessosTab" aria-selected="true"><i
                            class="fa fa-files-o"></i> {$parametros['nomenclatura']}s</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#pesquisaAnexosTab" role="tab"
                   aria-controls="pesquisaAnexosTab" aria-selected="false"><i class="fa fa-paperclip"></i> Anexos de
                    {$parametros['nomenclatura']}s</a>
            </li>
        </ul>
    </div>
    <div class="card-body tab-content">
        <div class="tab-pane active" id="pesquisaProcessosTab" role="tabpanel">
            <form id="formPesquisaProcesso" method="POST" class="form-horizontal"
                  action="{$app_url}src/App/View/Processo/listar.php">
                <input type="hidden" name="pesquisar" value="ok">
                <div class="card card-default">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-2">
                                <label>Exercício:</label>
                                <select name="exercicio" class="form-control select2">
                                    <option value="">Todos</option>
                                    {foreach $exercicios as $exercicio}
                                        <option value="{$exercicio}">{$exercicio}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Origem:</label>
                                <select name="origem" class="form-control select2">
                                    <option value="">Todas</option>
                                    {foreach App\Enum\OrigemProcesso::getOptions() as $value=>$text}
                                        <option value="{$value}">{$text}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Nº {$parametros['nomenclatura']}:</label>
                                <input type="number" name="numero_processo" class="form-control form-control-sm"/>
                            </div>
                            <div class="col">
                                <label>Status:</label>
                                <select class="form-control select2" name="status_id">
                                    <option value="">Todos</option>
                                    {foreach $status_processo as $status}
                                        <option value="{$status->getId()}">{$status}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6">
                                <label>Assunto:</label>
                                <div class="float-right">
                                    <a href="#" entidade="Assunto"
                                       title="Pesquisa avançada por Interessado"
                                       class="btn btn-xs btn-info btn-selectionar-entidade"><i
                                                class="fa fa-search"></i></a>
                                </div>
                                <select name="assunto_id" class="form-control select_assunto">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Interessado:</label>
                                <a href="#"
                                   title="Pesquisa avançada por Interessado"
                                   class="btn btn-xs btn-info btn-pesquisar-interessado pull-right"><i
                                            class="fa fa-search"></i></a>
                                <select id="" name="interessado_id" class="form-control select_interessado">
                                    <option value="">Todos</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label>Setor Origem:</label>
                                <select name="setor_origem_id" class="form-control select2">
                                    <option value="">Todos</option>
                                    {foreach $setores as $setor}
                                        <option value="{$setor->getId()}">{$setor}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col hidden">
                                <label>Setor Anterior:</label>
                                <select name="setor_anterior_id" class="form-control select2">
                                    <option value="">Todos</option>
                                    {foreach $setores as $setor}
                                        <option value="{$setor->getId()}">{$setor}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col">
                                <label>Setor Atual:</label>
                                <select name="setor_atual_id" class="form-control select2">
                                    <option value="">Todos</option>
                                    {foreach $setores as $setor}
                                        <option value="{$setor->getId()}">{$setor}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label>Responsável Abertura:</label>
                                <select id="select_cliente" name="responsavel_abertura_id" class="form-control select2">
                                    <option value="">Todos</option>
                                    {foreach $usuarios as $usuario}
                                        <option value="{$usuario->getId()}">{$usuario}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col">
                                <label>Responsável Atual:</label>
                                <select id="select_solicitante" name="responsavel_atual_id"
                                        class="form-control select2">
                                    <option value="">Selecione cliente</option>
                                    {foreach $usuarios as $usuario}
                                        <option value="{$usuario->getId()}">{$usuario}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label>Data abertura (de:)</label>
                                <input id="data_abertura_ini" data_fim_id="data_abertura_fim" type="text"
                                       name="data_abertura_ini" autocomplete="off"
                                       class="form-control form-control-sm date-range data"/>
                            </div>
                            <div class="col">
                                <label>Data abertura (até):</label>
                                <input id="data_abertura_fim" type="text" name="data_abertura_fim" autocomplete="off"
                                       class="form-control form-control-sm data"/>
                            </div>
                            <div class="col">
                                <label>Data trâmite (de):</label>
                                <input id="data_tramite_ini" data_fim_id="data_tramite_fim" type="text"
                                       name="data_tramite_ini" autocomplete="off"
                                       class="form-control form-control-sm date-range data"/>
                            </div>
                            <div class="col">
                                <label>Data trâmite (até):</label>
                                <input id="data_tramite_fim" type="text" name="data_tramite_fim" autocomplete="off"
                                       class="form-control form-control-sm data"/>
                            </div>
                            <div class="col">
                                <label>Data Arquivamento (de)</label>
                                <input id="data_arquivamento_ini" data_fim_id="data_arquivamento_fim" type="text"
                                       autocomplete="off"
                                       name="data_arquivamento_ini"
                                       class="form-control form-control-sm date-range data"/>
                            </div>
                            <div class="col">
                                <label>Data Arquivamento (até)</label>
                                <input id="data_arquivamento_fim" type="text" name="data_arquivamento_fim"
                                       autocomplete="off"
                                       class="form-control form-control-sm data"/>
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <label>Buscar por objeto/requerimento:</label>
                            <select name="tipo_pesquisa_objeto" class="">
                                <option value="0">Contém</option>
                                <option value="1">Inicia</option>
                                <option value="2">igual</option>
                            </select>
                            <textarea name="objeto" placeholder="Digite aqui uma palavra ou texto para pesquisar"
                                      class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary ladda-button" disabled="true"><i class="fa fa-search"></i>
                                Pesquisar 
                            </button>
                            <button type="reset" class="btn btn-light border"><i class="fa fa-refresh"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane" id="pesquisaAnexosTab" role="tabpanel">
            <form id="formPesquisaAnexoProcesso" method="POST" class="form-horizontal"
                  action="{$app_url}src/App/View/Anexo/pesquisar.php">
                <input type="hidden" name="pesquisar" value="ok">
                <div class="card card-default">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-1">
                                <label class="col-form-label">ID:</label>
                                <input type="text" name="id" class="form-control form-control-sm"/>
                            </div>
                            <div class="col-2">
                                <label class="col-form-label">Número:</label>
                                <input type="text" name="numero" class="form-control form-control-sm"/>
                            </div>
                            <div class="col-2">
                                <label class="col-form-label">Exercício:</label>
                                <select name="exercicio" class="form-control select2">
                                    <option value="">Todos</option>
                                    {foreach $exercicios as $exercicio}
                                        <option value="{$exercicio}"
                                                {if $exercicio eq $exercicio_atual}selected{/if}>{$exercicio}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-3">
                                <label class="col-form-label">Tipo de Anexo:</label>
                                <select name="tipo_anexo_id" class="form-control select2">
                                    <option value="">Todos</option>
                                    {foreach $tipos_anexo as $tipo}
                                        <option value="{$tipo->getId()}">{$tipo}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col">
                                <label>Valor R$ (de)</label>
                                <input type="text"
                                       name="valor_ini"
                                       class="form-control form-control-sm autonumeric"/>
                            </div>
                            <div class="col">
                                <label>Valor R$ (até)</label>
                                <input type="text"
                                       name="valor_fim"
                                       class="form-control form-control-sm autonumeric"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col">
                                <label>Data (de:)</label>
                                <input id="data_anexo_ini" data_fim_id="data_anexo_fim" type="text"
                                       name="data_anexo_ini" autocomplete="off"
                                       class="form-control form-control-sm date-range data"/>
                            </div>
                            <div class="col">
                                <label>Data (até):</label>
                                <input id="data_anexo_fim" type="text" name="data_anexo_fim" autocomplete="off"
                                       class="form-control form-control-sm data"/>
                            </div>
                            <div class="col">
                                <label>Data upload (de):</label>
                                <input id="data_upload_ini" data_fim_id="data_upload_fim" type="text" autocomplete="off"
                                       name="data_upload_ini"
                                       class="form-control form-control-sm date-range data"/>
                            </div>
                            <div class="col">
                                <label>Data upload (até):</label>
                                <input id="data_upload_fim" type="text" name="data_upload_fim" autocomplete="off"
                                       class="form-control form-control-sm data"/>
                            </div>
                            <div class="col">
                                <label>Qtde. Páginas (de)</label>
                                <input type="number"
                                       name="qtde_paginas_ini"
                                       class="form-control form-control-sm"/>
                            </div>
                            <div class="col">
                                <label>Qtde. Páginas (até)</label>
                                <input type="number"
                                       name="qtde_paginas_fim"
                                       class="form-control form-control-sm"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Buscar por conteúdo:</label>
                            <textarea name="conteudo_anexo"
                                      placeholder="Digite aqui uma palavra ou texto para pesquisar"
                                      class="form-control"></textarea>
                            <div class="alert alert-warning alert-warning border mt-2">
                                <span class="lead">Refine o resultado da pesquisa:</span><br>
                                <strong> Frase específica: </strong> Coloque a frase entre aspas duplas. Exemplo:
                                <strong>"código de
                                    obras"</strong><br>
                                <strong> Excluir palavra: </strong> O sinal menos(-) deve preceder a palavra. Exemplo:
                                <strong>código -
                                    obras</strong>

                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group text-right">
                            <button type="submit" disabled class="btn btn-primary ladda-button"><i class="fa fa-search"></i>
                                Pesquisar 
                            </button>
                            <button type="reset" class="btn btn-light border"><i class="fa fa-refresh"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script defer type="text/javascript" src="{$app_url}min/g=datatableButtonsJs&{$file_version}"></script>
