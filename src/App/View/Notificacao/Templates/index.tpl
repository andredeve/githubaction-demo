<div class="row">
    <div class="col-md-2">
        {if $usuario_logado->getTipo() neq \App\Enum\TipoUsuario::USUARIO and $usuario_logado->getTipo() neq \App\Enum\TipoUsuario::VISITANTE}
            <a title="Criar uma nova notificação" class="btn btn-success btn-loading btn-block" href="{$app_url}notificacao/cadastrar"><i class="fa fa-pencil"></i> Escrever</a>
        {else}
            <a title="Criar uma nova notificação" class="btn btn-success btn-loading btn-block disabled" href="#"><i class="fa fa-pencil"></i> Escrever</a>
        {/if}
        <br/>
        <div class="list-group">
            <a href="{$app_url}notificacao/index/{\App\Enum\ClassificacaoNotificacao::RECEBIDA}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {if $selected eq \App\Enum\ClassificacaoNotificacao::RECEBIDA}active{/if}"><span><i class="fa fa-envelope-open-o"></i> Recebidas</span> <span class="badge badge-warning badge-pill">{$qtde_notificacoes_recebidas}</span></a>
            <a href="{$app_url}notificacao/index/{\App\Enum\ClassificacaoNotificacao::ENVIADA}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {if $selected eq \App\Enum\ClassificacaoNotificacao::ENVIADA}active{/if}"><span><i class="fa fa-send-o"></i> Enviadas</span> <span class="badge badge-warning badge-pill">{$qtde_notificacoes_enviadas}</span></a>
            <a href="{$app_url}notificacao/index/{\App\Enum\ClassificacaoNotificacao::ARQUIVADA}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {if $selected eq \App\Enum\ClassificacaoNotificacao::ARQUIVADA}active{/if}"><span><i class="fa fa-archive"></i> Arquivadas</span> <span class="badge badge-warning badge-pill">{$qtde_notificacoes_arquivadas}</span></a>
        </div>
    </div>
    <div class="col">
        <div class="row">
            <div class="col">
                <span class="lead"><i class="fa fa-send-o"></i> Notificações {$selected}</span> | <small class="text-muted">{$hoje}</small>
            </div>
            <div class="col text-right">
                <a class="btn btn-warning btn-loading" href="#" onclick="window.location.reload();"><i class="fa fa-refresh"></i> Atualizar</a>
                {if $selected neq \App\Enum\ClassificacaoNotificacao::ARQUIVADA}
                    <a class="btn btn-info btn-arquivar-notificacao" href="#"><i class="fa fa-archive"></i> Arquivar</a>
                {/if}
                {if $usuario_logado->getTipo() neq \App\Enum\TipoUsuario::USUARIO and $usuario_logado->getTipo() neq \App\Enum\TipoUsuario::VISITANTE}
                    <a class="btn btn-danger btn-excluir-notificacao" href="#"><i class="fa fa-trash-o"></i> Excluir</a>
                {/if}       
            </div>
        </div>
        <br/>
        <form id="notificacoesTableForm" method="POST">
            <table id="tabelaNotificacoes" class="table table-bordered table-sm text-center table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>
                            <input type="checkbox" onclick="marcaCheckBox('sel-notificacao');"/>
                        </th>
                        <th>Nº</th>
                        <th class="text-left">
                            <i class="fa fa-user-o"></i> 
                            {if $selected eq \App\Enum\ClassificacaoNotificacao::RECEBIDA or ($selected eq \App\Enum\ClassificacaoNotificacao::ARQUIVADA and $usuario_logado->getTipo() eq \App\Enum\TipoUsuario::USUARIO)}
                                Remetente
                            {else}
                                Destinatário
                            {/if}
                        </th>
                        <th><i class="fa fa-folder-open-o"></i> {$nomenclatura}</th>
                        <th class="text-left"><i class="fa fa-comment-o"></i> Assunto</th>
                        <th><i class="fa fa-calendar-o"></i> Data</th>
                        <th><i class="fa fa-clock-o"></i> Prazo</th>
                            {if $selected eq \App\Enum\ClassificacaoNotificacao::ARQUIVADA}
                            <th><i class="fa fa-archive"></i> Dt. Arquivamento</th>
                            {else}
                            <th class="invisible hidden"></th>
                            {/if}
                            {if $selected eq \App\Enum\ClassificacaoNotificacao::ENVIADA}
                            <th>Status</th>
                            {else}
                            <th class="invisible hidden"></th>
                            {/if}
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $notificacoes_{$selected} as $notificacao}
                        <tr notificacao_id="{$notificacao->getId()}" class="linha-notificacao">
                            <td class="text-center col-actions">
                                <input type="checkbox" name="notificacao_id[]" value="{$notificacao->getId()}" class="sel-notificacao"/>
                            </td>
                            <td>{$notificacao->getNumero()}</td>
                            <td class="text-left">
                                {if $selected eq \App\Enum\ClassificacaoNotificacao::RECEBIDA or ($selected eq \App\Enum\ClassificacaoNotificacao::ARQUIVADA and $usuario_logado->getTipo() eq \App\Enum\TipoUsuario::USUARIO)}
                                    {$notificacao->getUsuarioAbertura()}
                                {else}
                                    {$notificacao->getUsuarioDestino()}
                                {/if}
                            </td>
                            <td>{$notificacao->getProcesso()}</td>
                            <td class="text-left">{$notificacao->getAssunto()}</td>
                            <td>{$notificacao->getDataCriacao()->format('d/m/Y')}</td>
                            <td>
                                <div style="width: 15px;height: 15px;float: left" class="{if $notificacao->getPrazoResposta() gt $data_atual}bg-success{else}bg-danger{/if}"></div>
                                {$notificacao->getPrazoResposta()->format('d/m/Y')}
                            </td>
                            {if $selected eq \App\Enum\ClassificacaoNotificacao::ARQUIVADA}
                                <td>{$notificacao->getDataArquivamento()->format('d/m/Y')}</td>
                            {else}
                                <td class="invisible hidden"></td>
                            {/if}
                            {if $selected eq \App\Enum\ClassificacaoNotificacao::ENVIADA}
                                <td>
                                    {$notificacao->getStatus()}
                                </td>
                            {else}
                                <td class="invisible hidden"></td>
                            {/if}
                            <td class="col-actions">
                                {if $selected eq \App\Enum\ClassificacaoNotificacao::RECEBIDA and !empty($notificao) and $notificao->getIsRespondida() eq false}
                                    <a title="Responder" class="btn btn-xs btn-light btn-outline-info" href="#">
                                        <i class="fa fa-reply"></i>
                                    </a>
                                {/if}
                                <a target="_blank" title="Imprimir" class="btn btn-xs btn-light btn-outline-info" href="{$app_url}notificacao/imprimir/id/{$notificacao->getId()}"><i class="fa fa-print"></i></a>
                                <a title="Visualizar" class="btn btn-xs btn-light btn-outline-info btn-loading" href="{$app_url}notificacao/visualizar/id/{$notificacao->getId()}"><i class="fa fa-search"></i></a>
                                    {if $notificacao->getPermissaoArquivar()}
                                    <a notificacao_id="{$notificacao->getId()}" title="Arquivar" class="btn btn-xs btn-light btn-outline-info btn-arquivar-notificacao" href="#">
                                        <i class="fa fa-archive"></i>
                                    </a>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </form>
    </div>
</div>