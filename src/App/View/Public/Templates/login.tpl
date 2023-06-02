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
    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=loginCss">
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
                <form method="POST" id="loginForm" action="{$app_url}usuario/autenticar">
                    <input type="hidden" id="app_url" name="app_url" value="{$app_url}"/>
                    {$messages}
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-user"></i></div>
                            </div>
                            <input type="text" name="login" class="form-control form-control-lg login_mask" autofocus="true"
                                   placeholder="Usuário ou CPF/CNPJ" required="true"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-lock"></i></div>
                            </div>
                            <input type="password" name="senha" class="form-control form-control-lg" placeholder="Senha"
                                   required="true"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <img src="{$app_url}src/App/View/Public/captcha.php" alt="Código Captcha LxCaptcha"><br>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="fa fa-check"></i></div>
                            </div>
                            <input type="text" name="captcha" class="form-control form-control-lg" autofocus="true"
                                            placeholder="Digite o Código da Imagem" required="true"/>
                            <button class="btn btn-info" type="button" id="captcha-reload"><i class="fa fa-refresh"></i></button>
                        </div>
                    </div>  
                    <div class="form-group mt-2">
                        <button type="submit" class="btn btn-info btn-block btn-lg ladda-button" data-style="zoom-in"><i
                                    class="glyphicon glyphicon-log-in"></i> Entrar
                        </button>
                    </div>
                    {if $contribuinteHabilitado}
                        <p>
                            Primeiro acesso {$parametros['contribuinte']}? <a href="{$app_url}contribuinte/termoUso">Clique aqui</a>
                        </p>
                    {/if}
                    <p>
                        Esqueceu sua senha? <a href="#alteraSenhaModal" data-toggle="modal">Clique aqui</a>
                    </p>
                    <p class="alert alert-warning">
                        Em caso de acesso de {$parametros['contribuinte']},<br> o usuário é o CPF/CNPJ.
                    </p>
                </form>
            </div>
        </div>

        <p class="text-center footer invisible">
            <small> © 2017. Desenvolvido pela <em><a title="Visitar site" target="_blank"
                                                     href="{$app_config['author_link']}">{$app_config['app_author']}</a></em>.
            </small>
        </p>
    </div>
</div>
{include file="senha.tpl"}
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript" src="{$app_url}min/g=loginJs"></script>
</body>
</html>
