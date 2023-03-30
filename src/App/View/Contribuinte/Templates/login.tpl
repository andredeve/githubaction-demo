<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{$app_config['app_name']} | {$page_title}</title>
    <link rel="shortcut icon" href="{$app_url}assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="{$app_url}assets/img/favicon.ico" type="image/x-icon">
    <!-- Tell the browser to be responsive to screen width -->
    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=contribuinteLoginCss">
    {*    <!-- CSS Aplicação -->*}
    <link rel="stylesheet" href="{$app_url}assets/css/app.css?v={$file_version}">
    <link rel="stylesheet" href="{$app_url}assets/css/custom.css?v={$file_version}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
</head>
<body>
<div class="block">
    <div class="centered">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <img alt="[Logo App]" src="{$app_url}assets/img/logo.png"/>
                </div>
                <p class="text-muted text-center">Digite seu usuário e senha para acessar:</p>
                <form method="POST" id="loginForm" action="{$app_url}interessado/autenticar">
                    <input type="hidden" id="app_url" name="app_url" value="{$app_url}"/>
                    <input type="hidden" id="is_processo_externo" value="true"/>
                    {$messages}
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-user"></i></div>
                            </div>
                            <input type="text" name="login" class="form-control form-control-lg cpf_cnpj" autofocus="true" placeholder="CPF / CNPJ" required="true"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-lock"></i></div>
                            </div>
                            <input type="password" name="senha" class="form-control form-control-lg" placeholder="Senha" required="true"/>
                        </div>
                    </div>
                    <!--<div  class="g-recaptcha" data-sitekey="6LfW_tgUAAAAALMCsTbYdDxgV6-XvTlF7HYzOsSk" ></div>-->
                    <div class="g-recaptcha" data-sitekey="{$data_site_key}"></div>
                    <div class="form-group mt-2">
                        <button type="submit" class="btn btn-info btn-block btn-lg ladda-button" data-style="zoom-in">
                            <i class="glyphicon glyphicon-log-in"></i> Acessar
                        </button>
                    </div>
                    <p>
                        É seu primeiro acesso? <a href="{$app_url}contribuinte/signup">Clique aqui</a>
                    </p>
                    <p>
                        Esqueceu sua senha? <a href="#modal_alterar_senha" data-toggle="modal">Clique aqui</a>
                    </p>
                </form>
            </div>
            {include file="../../Public/Templates/loading.tpl"}
        </div>
        <p class="text-center footer invisible">
            <small> © 2017. Desenvolvido pela
                <em>
                    <a title="Visitar site" target="_blank" href="{$app_config['author_link']}">{$app_config['app_author']}</a>
                </em>.
            </small>
        </p>
    </div>
</div>
{include file="modal_alterar_senha.tpl"}
<input type="hidden" id="app_path" value="{$app_url}"/>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript" src="{$app_url}min/g=contribuinteLoginJs"></script>
<script type="text/javascript" src="{$app_url}assets/js/view/contribuinte/formulario.js?v={$file_version}"></script>
<script type="text/javascript" src="{$app_url}assets/js/app.js?v={$file_version}"></script>
<script type="text/javascript" src="{$app_url}assets/js/custom.js?v={$file_version}"></script>
<script type="text/javascript" src="{$app_url}min/g=pluginsJs&{$file_version}"></script>
</body>
</html>
