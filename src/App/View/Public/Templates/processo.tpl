<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="{$app_url}min/g=consultaCss?v=3">      
    <link rel="icon" href="{$app_url}assets/img/favicon.ico" type="image/x-icon">
    <title>Consulta Pública de {$nomenclatura}s - {$cliente_config['descricao']}</title>
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
<main role="main" class="flex-shrink-0">
    <div class="container">
        <div class="row">
            <nav aria-label="breadcrumb">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{$app_url}consulta">Início</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Consulta de {$nomenclatura} nº {$processo}</li>
                    </ol>
                </div>
            </nav>
        </div>
        <br>
        <br>
        <br>
        <p class="lead"><i class="fa fa-file-o"></i> {$nomenclatura} {$processo->getNumero()}
            /{$processo->getExercicio()}
            {if $processo->getSigilo() != \App\Enum\SigiloProcesso::SEM_RESTRICAO}
                <span class="badge badge-danger"><i class="fa fa-lock"> {$optionsSigiloso = \App\Enum\SigiloProcesso::getOptions()}{$optionsSigiloso[$processo->getSigilo()]}</i></span>
            {/if}
            {if $processo->getIsArquivado() eq true}
                <span class="badge badge-secondary"><i class="fa fa-archive"> Arquivado</i></span>
            {/if}
            {if $processo->getApensado() neq null}
                <small class="float-right"><i class="fa fa-paperclip"></i>
                    Apensado ao Processo:
                    <button title="Visualizar Processo" class="btn btn-link" type="button"
                            onclick="visualizarProcesso({$processo->getApensado()->getId()})"><i
                                class="fa fa-file-o"></i> {$processo->getApensado()}
                    </button>
                </small>
            {/if}
        </p>
        {if $processo->getIsArquivado() eq true}
            <div class="alert alert-info">
                {$nomenclatura} arquivado em {$processo->getDataArquivamento(true)}<br/>
                <small><strong>Justificativa: </strong>{$processo->getJustificativaEncerramento()}</small>
            </div>
        {/if}
        <table class="table table-sm">
            <tr>
                <th class="w-25">Origem:</th>
                <td>{\App\Enum\OrigemProcesso::getDescricao($processo->getOrigem())}</td>
            </tr>
            <tr>
                <th class="w-25">Sigiloso?</th>
                <td>{if $processo->getIsSigiloso() eq true}Sim{else}Não{/if}</td>
            </tr>
            <tr>
                <th class="w-25">Assunto:</th>
                <td>{$processo->getAssunto(true)}</td>
            </tr>
            <tr>
                <th class="w-25">Interessado:</th>
                <td>{$processo->getInteressado()}</td>
            </tr>
            <tr>
                <th class="w-25">Setor Origem:</th>
                <td>{$processo->getSetorOrigem()}</td>
            </tr>
            <tr>
                <th class="w-25">Setor Atual:</th>
                <td>{$processo->getSetorAtual()}</td>
            </tr>
            <tr>
                <th class="w-25">Responsável abertura:</th>
                <td>{$processo->getUsuarioAbertura()}</td>
            </tr>
            <tr>
                <th class="w-25">Data abertura:</th>
                <td>{$processo->getDataAbertura()->format('d/m/Y')}</td>
            </tr>
            <tr>
                <th class="w-25">Data vencimento:</th>
                <td>{$processo->getDataVencimento()->format('d/m/Y')}</td>
            </tr>
            <tr>
                <th class="w-25">Objeto:</th>
                <td>{nl2br($processo->getObjeto())}</td>
            </tr>
            <tr>
                <th class="w-25">Responsável atual:</th>
                <td>{$processo->getResponsavel()}</td>
            </tr>
            <tr>
                <th class="w-25">Parecer atual:</th>
                <td>{nl2br($processo->getParecer())}</td>
            </tr>
        </table>
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#processoTabView" role="tab"
                           aria-controls="processoTabView" aria-selected="true"><i class="fa fa-info-circle"></i>
                            Descrição do
                            {$nomenclatura}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#anexosTabView" role="tab"
                           aria-controls="anexosTabView"
                           aria-selected="false"><i class="fa fa-paperclip"></i> Documentos <span
                                    id="qtde_anexos_processo"
                                    class="badge badge-primary">{count($processo->getAnexosConsultaPublica())}</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tramitesTabView" role="tab"
                           aria-controls="tramitesTabView"
                           aria-selected="false"><i class="fa fa-history"></i> Movimentação <span id="qtde_tramites"
                                                                                                  class="badge badge-primary">{count($processo->getTramites())}</span></a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#apensosTabView{$processo->getId()}" role="tab"
                       aria-controls="apensosTabView{$processo->getId()}"
                       aria-selected="false"><i class="fa fa-files-o"></i> Apensos <span
                                id="qtde_processo_vinculados"
                                class="badge badge-primary">{count($processo->getApensos())}</span></a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="processoTabView" role="tabpanel">
                        {$processo->getObjeto()}
                    </div>
                    <div class="tab-pane" id="anexosTabView" role="tabpanel">
                        {$consultar=true}
                        <div class="table-responsive">
                            {include file="../../Anexo/Templates/listar.tpl"}
                        </div>
                    </div>
                    <div class="tab-pane" id="tramitesTabView" role="tabpanel">
                        <div class="table-responsive">
                            {include file="../../Tramite/Templates/listar.tpl"}
                        </div>
                    </div>
                    <div class="tab-pane" id="apensosTabView{$processo->getId()}" role="tabpanel">
                        {$resultado=$processo->getApensos()->toArray()}
                        {include file="../../Processo/Templates/listar.tpl"}
                    </div>
                </div>
                <br>
                <br>
                <br>
            </div>
        </div>
        <br> 
        <br>
        <br>
    </div>
</main>
<input type="hidden" id="app_url" value="{$app_url}"/>
<script src="{$app_url}min/g=consultaJs?v=300820221400"></script>
</body>
</html>