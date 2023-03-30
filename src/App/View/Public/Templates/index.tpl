<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{$app['app_name']} v.{$app['app_version']} {if isset($page_title)}| {$page_title}{/if}</title>
    <link rel="icon" href="{$app_url}assets/img/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=pluginsCss&{$file_version}">
    <!-- CSS Aplicação -->
    <link rel="stylesheet" href="{$app_url}assets/css/body_pattern.css?v={$file_version}">
    <link rel="stylesheet" href="{$app_url}assets/css/app.css?v={$file_version}">
    <link rel="stylesheet" href="{$app_url}assets/css/custom.css?v={$file_version}">
    <!-- DataTables plugin Select -->
    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=datatableSelectCss">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
{*    <script type="module" src="https://unpkg.com/x-frame-bypass"></script>*}
</head>
<body>
{if !empty($usuario_logado)}
    {if $usuario_logado->getTipo() == App\Enum\TipoUsuario::INTERESSADO}
        {include file="../../Contribuinte/Templates/header.tpl"}
    {else}
        {include file="header.tpl"}
    {/if}
{/if}
<div id="corpo" style="height: 100%;">
    
    {if isset($url_assinatura) && !empty($url_assinatura)}
        <form class="hidden" id="moodleform" target="iframe"
                method="post" action="{$url_assinatura}" >
           <input type="text" name="sistema" value="lxprocessos" />
           <input type="text" name="j_password" value="password" />
           <input type="text"  name="token_user" value="{$token_user}"/>
           <input type="text"  name="integracao" value="{$usuario_id}"/>
            {if $usuario }
                <input type="hidden" name="token_lxsign" value="{$app['token_lxsign']}" />
                <input type="hidden"  name="usuario_nome" value="{$usuario->getPessoa()->getNome()}" />
                <input type="hidden"  name="usuario_cargo" value="{$usuario->getCargo()}" />
                <input type="hidden"  name="usuario_email" value="{$usuario->getPessoa()->getEmail()}" />
                <input type="hidden"  name="usuario_login" value="{$usuario->getLogin()}" />
                />
            {/if}

        </form>
           <iframe id="iframeLxsign" seamless="seamless" name="iframe" frameborder="0" on  style="min-height: 900px;overflow:hidden;width:100%" height="100%" width="100%"
                   ></iframe>
        <script defer="true" type="text/javascript">
            
            function setIframeHeight(iframe) {
                if (iframe) {
                    var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
                    if (iframeWin.document.body) {
                        iframe.height = iframeWin.document.body.scrollHeight;
                    }
                }
            };
            window.onload = function () {
                setIframeHeight(document.getElementById('iframeLxsign'));
                {*document.querySelector("#iframeLxsign").addEventListener( "load", function(e) {
                    setIframeHeight(document.getElementById('iframeLxsign'));
                    

                } );*}
            };
            document.getElementById('moodleform').submit(
                {
                    success: function (response){
                        console.log(response);
                    }
                }
            );
            let form = document.getElementById('moodleform');
            let frame = document.getElementById('iframeLxsign');

            //frame.contentWindow.postMessage([], form.getAttribute("action"));
{*            frame.contentWindow.postMessage([], 'https://lxsign.com.br/dev/');*}
        </script>
    {else}
        <div class="container">
            <main role="main">
                <div class="card">
                    {if isset($page_title)}
                        <div class="card-header content-header pl-4 pr-4 pt-2 pb-2">
                            <div class="row">
                                <h6 class="col">
                                    {if isset($page_icon)}<i class="{$page_icon}"></i>{/if}&nbsp;{$page_title}
                                    {if isset($page_description)}
                                        <br/>
                                        <small class="text-muted">{$page_description}</small>{/if}
                                </h6>
                                <div class="col text-right">
                                    <ol class="breadcrumb">
                                        {if isset($breadcrumb)}
                                            <li class="breadcrumb-item"><a href="{$app_url}"><i class="fa fa-home"></i>
                                                    Início</a></li>
                                            {foreach $breadcrumb as $b}
                                                {if $b@last}
                                                    <li class="breadcrumb-item active">{$b['title']}</li>
                                                {else}
                                                    <li class="breadcrumb-item"><a
                                                                href="{$app_url}{$b['link']}">{$b['title']}</a></li>
                                                {/if}
                                            {/foreach}
                                        {else}
                                            <li class="breadcrumb-item active">
                                                <a href="{$app_url}"><i class="fa fa-home"></i> Início</a>
                                            </li>
                                        {/if}
                                    </ol>
                                </div>
                            </div>
                        </div>
                    {/if}
                    <div id="conteudo" class="card-body {if isset($page_class)}{$page_class}{/if}">
                        {$messages}
                        <div id="divAjaxUpdate">
                            {include file=$conteudo}
                        </div>
                        {include file="loading.tpl"}
                    </div>
                </div>
            </main>
        </div>
    {/if}
</div>
{include file="footer.tpl"}
<!-- Modal com formulário para alterar senha de usuário logado -->
{include file="senha.tpl"}
<!-- Modal com formulário para contato com equipe de suporte  -->
{include file="suporte.tpl"}
{* Variáveis Globais Jquery do cliente *}
<input type="hidden" id="nomenclatura" value="{$parametros['nomenclatura']}"/>
<input type="hidden" id="usuarioEhInteressado" value="{$usuarioEhInteressado}"/>
<input type="hidden" id="nome_cliente" value="{$cliente_config['nome']}"/>
<input type="hidden" id="endereco_cliente" value="{$cliente_config['endereco']}"/>
<input type="hidden" id="telefone_cliente" value="{$cliente_config['telefone']}"/>
<input type="hidden" id="cnpj_cliente" value="{$cliente_config['cnpj']}"/>
<input type="hidden" id="base64_logo" value="{$cliente_config['base64_logo']}"/>
{* Outras Variáveis Globais*}
<input type="hidden" id="app_path" value="{$app_url}"/>
{if isset($lxsign_url)}
    <input type="hidden" id="lxsign_url" value="{$lxsign_url}"/>
{/if}
<input type="hidden" id="is_anexo_selecionado" value=""/>

<!-- App Scripts -->
{if !empty($usuario_logado) || !empty($usuarioEhInteressado)}
    <script type="text/javascript" src="{$app_url}min/g=pluginsJs&{$file_version}"></script>
    <script defer type="text/javascript" src="{$app_url}assets/js/app.js?v={$file_version}"></script>
    <script defer type="text/javascript" src="{$app_url}min/g=datatableSelectJs"></script>
    <script defer type="text/javascript" src="{$app_url}assets/js/custom.js?v={$file_version}"></script>
    <script defer type="text/javascript" src="{$app_url}assets/js/anexo.js?v={$file_version}"></script>
{/if}    

</body>
</html>
