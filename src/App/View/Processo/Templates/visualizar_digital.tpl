<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Processo {$processo}</title>
    <link rel="icon" href="{$app_url}assets/img/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=pluginsCss&{$file_version}">
    <style>
        html {
            height: 100%;
        }

        body {
            min-height: 100%;
        }

        #object_pdf {
            width: 100%;
        }
    </style>
</head>

<body class="bg-light">
    {include file="../../Public/Templates/loading.tpl"}
    <div class="container-fluid">
        <h4 class="text-center text-primary p-2"><i class="fa fa-folder-open-o"></i> {$parametros['nomenclatura']}
            {$processo} -
            <small>{$processo->getAssunto()}</small>
        </h4>
        <div class="row">
            <div class="col-3">
                <div id="listaDocumentos" class="list-group overflow-auto" style="font-size: 12px;">
                    <div class="d-flex">
                        <input type="checkbox" class="m-1" name="{$processo->getAnexosPath()}{$processo->getCapa()}">
                        <a href="{$processo->getAnexoUrl()}{$processo->getCapa()}"
                            class="list-group-item list-group-item-action  {if !$processo->getApensado()} active{/if}  arquivo-processo">
                            <i class="fa fa-file"></i> <strong> {if $processo->getApensado()} Apenso {$p} <br>{/if} Capa
                                do Processo</strong>
                        </a>
                    </div>
                    {foreach $processo->getComponentes(true, true) as $componente}
                        {if $componente->getTramite()}
                            {if $componente->getTramite()->gerarFormularioEletronico() eq true}
                                <input type="checkbox" class="m-1" name="{$processo->getAnexosPath()}{$componente->getTramite()->getNomeArquivoFormularioEletronico()}">
                                <a href="{$processo->getAnexoUrl()}{$componente->getTramite()->getNomeArquivoFormularioEletronico()}"
                                    class="list-group-item list-group-item-action arquivo-processo">
                                    <strong>
                                        <i class="fa fa-file-text-o"></i>
                                        Formulário Eletrônico
                                    </strong>
                                    <br />
                                    <small>
                                        <i class="fa fa-clock-o"></i>
                                        {$componente->getTramite()->getDataEnvio()->format('d/m/Y - H:i')}

                                    </small>
                                </a>
                            {/if}
                        {else if $componente->getAnexo()}
                            {if !is_int($componente->getAnexo()->getTamanho())}
                                <div class="d-flex">
                                    <input type="checkbox" class="m-1" name="{$componente->getAnexo()->getArquivoCarimbado()}">
                                    <a href="{$componente->getAnexo()->getArquivoCarimbado(true)}?v={time()}"
                                        class="list-group-item list-group-item-action arquivo-processo">
                                        <strong style="text-transform: capitalize;">
                                            <i class="fa fa-file-text-o"></i>
                                            {$componente->getAnexo()->getTipo()} - {$componente->getAnexo()->getDescricao()}
                                        </strong>
                                        <br />
                                        <small>
                                            Número: {$componente->getAnexo()->getNumero()}
                                            {" | "}
                                            <i class="fa fa-clock-o"></i>
                                            {$componente->getAnexo()->getDataCadastro()->format('d/m/Y - H:i')}
                                            {" | "}
                                            <i class="fa fa-paperclip"></i> {$componente->getAnexo()->getTamanho()}
                                        </small>
                                    </a>
                                </div>
                            {/if}
                        {/if}
                    {/foreach}
                    {foreach $processo->getApensos() as $apenso}
                        {$apenso->gerarCapa()}
                        <div class="d-flex">
                            <input type="checkbox" class="m-1" name="{$apenso->getAnexosPath()}{$apenso->getCapa()}">
                            <a href="{$apenso->getAnexoUrl()}{$apenso->getCapa()}"
                                class="list-group-item list-group-item-action  {if !$processo->getApensado()} active{/if}  arquivo-processo">
                                <i class="fa fa-file"></i> <strong> {if $apenso->getApensado()} Apenso {$p} <br>{/if} Capa
                                    do Processo</strong>
                            </a>
                        </div>
                        {foreach $apenso->getComponentes(false, true) as $componente}
                            {if $componente->getTramite()}
                                {if $componente->getTramite()->gerarFormularioEletronico() eq true}
                                    <a href="{$apenso->getAnexoUrl()}{$componente->getTramite()->getNomeArquivoFormularioEletronico()}"
                                        class="list-group-item list-group-item-action arquivo-processo">
                                        <strong>
                                            <i class="fa fa-file-text-o"></i>
                                            Formulário Eletrônico
                                        </strong>
                                        <br />
                                        <small>
                                            <i class="fa fa-clock-o"></i>
                                            {$componente->getTramite()->getDataEnvio()->format('d/m/Y - H:i')}

                                        </small>
                                    </a>
                                {/if}
                            {else if $componente->getAnexo()}
                                {if !is_int($componente->getAnexo()->getTamanho())}
                                    <div class="d-flex">
                                        <input type="checkbox" class="m-1" name="{$componente->getAnexo()->getArquivoCarimbado()}">
                                        <a href="{$componente->getAnexo()->getArquivoCarimbado(true)}?v={time()}"
                                            class="list-group-item list-group-item-action arquivo-processo">
                                            <strong style="text-transform: capitalize;">
                                                <i class="fa fa-file-text-o"></i>
                                                {$componente->getAnexo()->getTipo()} - {$componente->getAnexo()->getDescricao()}
                                            </strong>
                                            <br />
                                            <small>
                                                Número: {$componente->getAnexo()->getNumero()}
                                                {" | "}
                                                <i class="fa fa-clock-o"></i>
                                                {$componente->getAnexo()->getDataCadastro()->format('d/m/Y - H:i')}
                                                {" | "}
                                                <i class="fa fa-paperclip"></i> {$componente->getAnexo()->getTamanho()}
                                            </small>
                                        </a>
                                    </div>
                                {/if}
                            {/if}
                        {/foreach}
                    {/foreach}
                </div>
                <br />
                <button type="button" target="_blank" href="{$app_url}processo/download/id/{$processo->getId()}"
                    class="btn btn-warning btn-block btn-download-processo" processo-numero="{$processo->getNumero()}"
                    processo-exercicio="{$processo->getExercicio()}">
                    <i class="fa fa-download"></i> Baixar Processo Digital
                </button>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="btn-group col-md-12" role="group" aria-label="Basic example">
                                <button type="button" class="btn col-md-6  btn-outline-info btn-arquivo-anterior">
                                    <i class="fa fa-arrow-left"></i> Anterior
                                </button>
                                <button type="button" class="btn col-md-6  btn-outline-info btn-proximo-arquivo">
                                    Próximo <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        <iframe id="object_pdf" base_url="{$app_url}lib/pdfjs/web/viewer.html?file="
                            src="{$app_url}lib/pdfjs/web/viewer.html?file={$processo->getAnexoUrl()}{$processo->getCapa()}&_={uniqid()}"
                            class="embed-responsive-item" allowfullscreen></iframe>
                        <div class="row">
                            <div class="btn-group col-md-12" role="group" aria-label="Basic example">
                                <button type="button" class="btn col-md-6  btn-outline-info btn-arquivo-anterior">
                                    <i class="fa fa-arrow-left"></i> Anterior
                                </button>
                                <button type="button" class="btn col-md-6  btn-outline-info btn-proximo-arquivo">
                                    Próximo <i class="fa fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{$app_url}min/g=pluginsJs&v={$file_version}"></script>
    <!-- App Scripts -->
    <script defer type="text/javascript" src="{$app_url}assets/js/app.js?v={$file_version}"></script>
    <!-- App Scripts -->
    <script type="text/javascript" src="{$app_url}lib/popper.min.js"></script>
    <script type="text/javascript" src="{$app_url}vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{$app_url}lib/pdfjs/build/pdf.js"></script>
    <script type="text/javascript" src="{$app_url}assets/js/digital.js?v={$file_version}"></script>
</body>

</html>
