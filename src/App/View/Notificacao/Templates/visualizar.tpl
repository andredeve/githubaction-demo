<h4 class="text-info pull-left">Notificação nº {$notificacao->getNumero()}/{$notificacao->getDataCriacao()->format('Y')}</h4>
<div class="pull-right">
    <button type="button" onclick="history.go(-1);" class="btn btn-outline-secondary" ><i class="fa fa-arrow-left"></i> Voltar</button>
    {if $notificacao->getPermissaoResponder() eq true}
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#responderNotificacaoModal"><i class="fa fa-reply"></i> Responder</button>
    {/if}
    <a target="_blank" class="btn btn-warning" href="{$app_url}notificacao/imprimir/id/{$notificacao->getId()}"><i class="fa fa-print"></i> Imprimir</a>
    {if $notificacao->getPermissaoArquivar() eq true}
       {* <a class="btn btn-light border" href="#"><i class="fa fa-archive"></i> Arquivar</a>*}
    {/if}
    <a class="btn btn-danger btn-excluir" title="Excluir" href="{$app_url}Notificacao/excluir/id/{$notificacao->getId()}"><i class="fa fa-trash-o"></i> Excluir</a>
</div>
<div class="clearfix"></div>
<br/>
<div class="row">
    <div class="col-md-4">
        <div class='card'>
            <div class='card-header text-primary font-weight-light'><i class="fa fa-envelope-o"></i> Dados da Notificação</div>
            <table class="table">
                <tr>
                    <th>Status:</th><td>{$notificacao->getStatus()}</td>
                </tr>
                <tr>
                    <th>Data Criação:</th><td>{$notificacao->getDataCriacao()->format('d/m/Y - H:i')}</td>
                </tr>
                <tr>
                    <th>Prazo resposta:</th><td>{$notificacao->getPrazoResposta()->format('d/m/Y')}</td>
                </tr>
                <tr>
                    <th>Referente ao {$nomenclatura}:</th><td><a onclick="visualizarProcesso({if $notificacao->getProcesso()}{$notificacao->getProcesso()->getId()}{/if})" href="#"><i class='fa fa-search'></i> {$notificacao->getProcesso()}</a></td>
                </tr>
                <tr>
                    <th>Remetente:</th><td><i class="fa fa-user-o"></i> {$notificacao->getUsuarioAbertura()}</td>
                </tr>
                <tr>
                    <th>Destinatário:</th><td><i class="fa fa-user-o"></i> {$notificacao->getUsuarioDestino()}</td>
                </tr>
                <tr>
                    <th>Lida em:</th><td>{if $notificacao->getDataVisualizacao() neq null}{$notificacao->getDataVisualizacao()->format('d/m/Y - H:i')}{else}-{/if}</td>
                </tr>
                <tr>
                    <th>Respondida em:</th><td>{if $notificacao->getDataResposta() neq null}{$notificacao->getDataResposta()->format('d/m/Y - H:i')}{else}-{/if}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-md-8">
        <div class='card'>
            <div class='card-header text-primary font-weight-light'><i class="fa fa-comment-o"></i> Assunto</div>
            <div class="card-body lead">{$notificacao->getAssunto()}</div>
        </div>
        <br/>
        <div class='card'>
            <div class='card-header text-primary font-weight-light'><i class="fa fa-file-text-o"></i> Conteúdo</div>
            <div class="card-body" style="white-space: pre-line;">{$notificacao->getTexto()}</div>
        </div>
        {if $notificacao->getIsRespondida() eq true}
            <br/>
            <div class='card'>
                <div class='card-header text-primary font-weight-light'><i class="fa fa-reply"></i> Resposta</div>
                <div class="card-body">{$notificacao->getResposta()}</div>
            </div>
        {/if}
    </div>
</div>
<div id="responderNotificacaoModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Responder Notificação # {$notificacao->getNumero()}<br/><small>{$notificacao->getAssunto()}</small></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {include file="../../Notificacao/Templates/responder.tpl"}
            </div>
        </div>
    </div>
</div>


