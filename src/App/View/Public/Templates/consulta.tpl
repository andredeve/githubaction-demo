<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=consultaCss">
    <link rel="icon" href="{$app_url}assets/img/favicon.ico" type="image/x-icon">
    <title>Consulta Pública de Protocolos - {$cliente_config['descricao']}</title>
</head>
<body class="d-flex flex-column h-100">
<!-- Image and text -->
<nav class="navbar navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img width="50" height="40" class="d-inline-block align-top"
                 src="{$app_url}assets/img/brasao-mini.png"/>
            {$cliente_config['descricao']}
        </a>
    </div>
</nav>
<br/>
<main role="main" class="flex-shrink-0" style="font-size: 1rem">
    <div class="container">
        <h2 class="text-info">Consulta Pública de Protocolos</h2>
        <p class="lead">Favor informar um dos dados abaixo</p>
        <form id="consultaProcessoForm" method="POST">
            <div class="form-group row">
                <label class="col-md-2 col-sm-12 col-form-label">Exercício Protocolo:</label>
                <div class="col-md-4 col-sm-12">
                    <select class="form-control buscar-processo-group" name="anoProcesso">
                        <option value="">Selecione</option>
                        {foreach $anos as $ano}
                            <option value="{$ano}" {if $ano eq $ano_atual}selected{/if}>{$ano}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-sm-12 col-form-label">Número Protocolo:</label>
                <div class="col-md-4 col-sm-12">
                    <input type="number" name="numeroProcesso" class="form-control buscar-processo-group">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-form-label col-sm-12">
                    Interessado:                    
                </label>
                <div class="col-md-4 col-sm-12">
                    <a href="#"
                        title="Pesquisa avançada por Interessado"
                        class="btn btn-xs btn-light border btn-pesquisar-interessado"><i
                                class="fa fa-search"></i></a>

                    <select id="" name="interessadoProcesso" style="width: 88%!important;"
                            class="form-control select_interessado buscar-processo-group">
                        <option value="">Todos</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-sm-12 col-form-label">Descrição Objeto:</label>
                <div class="col-md-4 col-sm-12">
                    <textarea type="text" name="objetoProcesso" class="form-control buscar-processo-group"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-sm-12 col-form-label">C.I. Nº:</label>
                <div class="col-md-2 col-sm-12">
                    <input type="number" name="numeroCI" placeholder="Número"
                           class="form-control buscar-processo-group">
                </div>
                <label class="col-md-2 col-sm-12 col-form-label d-block d-sm-block d-md-none">CI Ano:</label>
                <div class="col-md-2 col-sm-12">
                    <input type="number" name="anoCI" placeholder="Ano" class="form-control buscar-processo-group">
                </div>
            </div>
            {*<div class="form-group row">
                <label class="col-sm-2 col-form-label">C.P.F/ C.N.P.J:</label>
                <div class="col-sm-4">
                    <input id="cpfcnpj" type="text" name="cpfCnpjProcesso" class="form-control buscar-processo-group">
                </div>
            </div>*}
            <div class="form-group row">
                <div class="col-md-4 col-sm-12 offset-md-2">
                    <div class="g-recaptcha" data-sitekey="{$app['data_site_key']}" data-size="normal"></div>
                    <button class="btn btn-primary btn-block mt-3 ladda-button" type="submit"><i
                                class="fa fa-search"></i> Consultar
                    </button>
                    <br> <br>
                </div>
            </div>
        </form>
    </div>
</main>
{include file="loading.tpl"}

<input type="hidden" id="app_url" value="{$app_url}"/>
<script src="{$app_url}min/g=consultaJs?v=2"></script>
<script src='https://www.google.com/recaptcha/api.js' async defer></script>
</body>
</html>