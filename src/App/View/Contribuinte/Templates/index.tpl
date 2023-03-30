<!DOCTYPE html>
<html lang="pt-br">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{$app_config['app_name']} | {$page_title}</title>
    <link rel="shortcut icon" href="{$app_url}assets/img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="{$app_url}assets/img/favicon.ico" type="image/x-icon">
{*    <!-- CSS Aplicação -->*}
    <link rel="stylesheet" href="{$app_url}assets/css/app.css?v={$file_version}">
    <link rel="stylesheet" href="{$app_url}assets/css/custom.css?v={$file_version}">
    <!-- Tell the browser to be responsive to screen width -->
    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=pluginsCss&{$file_version}">
    <link rel="stylesheet" href="{$app_url}assets/css/body_pattern.css?v={$file_version}">
{*    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=processoExternoCss">*}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
{if $interessado_logado}
    {include file="../../Contribuinte/Templates/header.tpl"}
{/if}
<div id="corpo" style="height: 100%;">
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
                                        <li class="breadcrumb-item"><a href="{$app_url}contribuinte/home"><i class="fa fa-home"></i>
                                                Início</a></li>
                                        {foreach $breadcrumb as $b}
                                            {if $b@last}
                                                <li class="breadcrumb-item active">{$b['title']}</li>
                                            {else}
                                                <li class="breadcrumb-item">
                                                    <a href="{$app_url}contribuinte/{$b['link']}">{$b['title']}</a>
                                                </li>
                                            {/if}
                                        {/foreach}
                                    {else}
                                        <li class="breadcrumb-item active">
                                            <a href="{$app_url}contribuinte/home"><i class="fa fa-home"></i> Início</a>
                                        </li>
                                    {/if}
                                </ol>
                            </div>
                        </div>
                    </div>
                {/if}
                <div id="conteudo" class="card-body">
{*                    {$messages}*}
                    <div id="divAjaxUpdate">
                        {include file=$conteudo}
                    </div>
                    {include file="../../Public/Templates/loading.tpl"}
                </div>
            </div>
        </main>
    </div>
</div>
<input type="hidden" id="app_path" value="{$app_url}"/>
<input type="hidden" id="is_processo_externo" value="true"/>
<input type="hidden" id="nomenclatura" value="{$parametros['nomenclatura']}"/>
{include file="../../Public/Templates/footer.tpl"}
{*<script src='https://www.google.com/recaptcha/api.js'></script>*}
<script type="text/javascript" src="{$app_url}min/g=pluginsJs&{$file_version}"></script>
<script defer type="text/javascript" src="{$app_url}assets/js/app.js?v={$file_version}"></script>
<script defer type="text/javascript" src="{$app_url}assets/js/custom.js?v={$file_version}"></script>
</body>
</html>
