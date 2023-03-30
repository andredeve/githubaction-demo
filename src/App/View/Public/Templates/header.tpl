<nav id="menu" class="navbar navbar-expand-md navbar-dark bg-blue-gradient p-1">
    <div class="container">
        <a target="_blank" class="navbar-brand" href="{$cliente_config['site']}">
            <img width="40" height="30" class="d-inline-block align-top" src="{$app_url}assets/img/brasao-mini.png"/>
            {$cliente_config['descricao']}
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse"
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse"
                data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                {if $usuario_logado && $usuario_logado->getGrupo()}
                    {if $usuario_logado->getGrupo()->getRelatorios() eq true or $usuario_logado->getTipo() eq  App\Enum\TipoUsuario::MASTER}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-center" href="#" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-line-chart "></i> Relatórios
                            </a>
                            <div class="dropdown-menu">
                                {foreach App\Controller\RelatorioController::getRelatorios() as $relatorio}
                                    <a class="dropdown-item btn-loading"
                                       href="{$relatorio['link']}">{$relatorio['descricao']}</a>
                                {/foreach}
                            </div>
                        </li>
                    {/if}
                {/if}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-center" href="#" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-database "></i> Cadastros
                    </a>
                    <div class="dropdown-menu">
                        <a title="Gerenciar assuntos de {$parametros['nomenclatura']}s" class="dropdown-item btn-loading"
                           href="{$app_url}assunto">Assuntos</a>
                        <a title="Gerenciar Categorias de documentos" class="dropdown-item btn-loading"
                           href="{$app_url}categoriaDocumento">Categorias de Documentos</a>
                        <a title="Gerenciar modelos de documentos" class="dropdown-item btn-loading"
                           href="{$app_url}modeloDocumento">Modelos de Documentos</a>
                        <a title="Gerenciar Classificações de CONARQ" class="dropdown-item btn-loading"
                           href="{$app_url}classificacao">Classificações CONARQ</a>
                        <a title="Gerenciar interessados dos {$parametros['nomenclatura']}s" class="dropdown-item btn-loading"
                           href="{$app_url}interessado">Interessados</a>
                        <a title="Gerenciar setores" class="dropdown-item btn-loading"
                           href="{$app_url}setor">Setores</a>
                        <a title="Configurar fluxograma(workflow) para assuntos de {$parametros['nomenclatura']}s"
                           class="dropdown-item btn-loading" href="{$app_url}fluxograma">Fluxograma de {$parametros['nomenclatura']}s</a>
                        <a title="Gerenciar status de {$parametros['nomenclatura']}s" class="dropdown-item btn-loading"
                           href="{$app_url}statusProcesso">Status de {$parametros['nomenclatura']}s</a>
                        <a title="Gerenciar tipos de anexos de {$parametros['nomenclatura']}s" class="dropdown-item btn-loading"
                           href="{$app_url}tipoAnexo">Tipos de Anexos</a>
                        <a title="Gerenciar Local de Arquivamento Físico" class="dropdown-item btn-loading"
                           href="{$app_url}local">Local de Arquivamento</a>
                        <a title="Gerenciar Tipos Local de Arquivamento Físico" class="dropdown-item btn-loading"
                           href="{$app_url}tipoLocal">Tipo de Local de Arquivamento</a>
                        <a title="Gerenciar SubTipos Local de Arquivamento Físico" class="dropdown-item btn-loading"
                           href="{$app_url}subTipoLocal">SubTipo de Local de Arquivamento</a>
                    </div>
                </li>
                {if !is_null($usuario_logado) && !in_array($usuario_logado->getTipo(), [App\Enum\TipoUsuario::USUARIO, App\Enum\TipoUsuario::VISITANTE])}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-center" href="#" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-users "></i> Usuários
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-loading"
                               title="Cadastrar um novo usuário para ter acesso ao sistema"
                               href="{$app_url}usuario/cadastrar"><i class="fa fa-user-plus"></i> Cadastrar</a>
                            <a class="dropdown-item btn-loading" title="Listar os usuários cadastrados"
                               href="{$app_url}usuario"><i class="fa fa-th-list"></i> Listar</a>
                            <a class="dropdown-item btn-loading" title="Listar os contribuintes cadastrados"
                               href="{$app_url}usuarioContribuinte"><i class="fa fa-th-list"></i> Contribuintes</a>   
                            <a class="dropdown-item btn-loading" title="Gerenciar grupos de usuário"
                               href="{$app_url}grupo"><i class="fa fa-group"></i> Grupos</a>
                        </div>
                    </li>
                {/if}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-center" href="#" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user-circle"></i> {$usuario_logado->getPessoa()->getPrimeiroNome()}
                    </a>
                    <div class="dropdown-menu">
                        <a title="Alterar informações da sua conta" class="dropdown-item btn-loading"
                           href="{$app_url}usuario/perfil"><i class="fa fa-user"></i> Perfil</a>
                        <a title="Alterar sua senha de acesso do sistema" class="dropdown-item btn-loading"
                           href="{$app_url}usuario/senha"><i class="fa fa-key"></i> Alterar senha</a>
                        <a title="Contatar suporte técnico do sistema" class="dropdown-item" href="#"
                           data-toggle="modal" data-target="#suporteModal"><i class="fa fa-wrench"></i> Suporte técnico</a>
                        <a title="Sair do sistema" class="dropdown-item btn-loading" href="{$app_url}usuario/sair"><i
                                    class="fa fa-sign-out"></i> Sair</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">Menu</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{$app_url}"><i class="fa fa-home "></i> Início </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-files-o "></i> {$parametros['nomenclatura']}s
                    </a>
                    <div class="dropdown-menu" style="min-width: 15rem">
                        <a class="dropdown-item btn-loading" href="{$app_url}processo/cadastrar">
                            <i class="fa fa-plus"></i> Novo
                        </a>
                        <a class="dropdown-item btn-loading" href="{$app_url}processo/pesquisar">
                            <i class="fa fa-search"></i> Pesquisar
                        </a>
                        <a class="dropdown-item btn-ver-vencidos" href="#">
                            <i class="fa fa-clock-o"></i> Vencidos
                            <span class="badge badge-primary pull-right qtde_processos_vencidos">{$qtde_vencidos}</span>
                        </a>
                        <a class="dropdown-item btn-loading" href="{$app_url}processo/enviados">
                            <i class="fa fa-send-o"></i> Enviados
                            <span class="badge badge-primary pull-right qtde_processos_enviados">{$qtde_enviados}</span>
                        </a>
                        <a class="dropdown-item btn-loading" href="{$app_url}processo/receber">
                            <i class="fa fa-envelope-open-o"></i> A Receber
                            <span class="badge badge-primary pull-right qtde_processos_receber">{$qtde_receber}</span>
                        </a>
                        <a class="dropdown-item btn-loading" href="{$app_url}processo/abertos">
                            <i class="fa fa-mail-forward"></i> Em Aberto
                            <span class="badge badge-primary pull-right qtde_processos_abertos">{$qtde_aberto}</span>
                        </a>
                        <a class="dropdown-item btn-loading" href="{$app_url}processo/arquivados">
                            <i class="fa fa-archive"></i> Arquivados
                            <span class="badge badge-primary pull-right qtde_processos_arquivados">{$qtde_arquivados}</span>
                        </a>
                        {if $contribuinteHabilitado}
                            <a class="dropdown-item btn-loading" href="{$app_url}processo/contribuintes">
                                <i class="fa fa-envelope"></i> Contribuintes (Recepção)
                                <span class="badge badge-primary pull-right qtde_processos_contribuintes">{$qtde_contribuintes}</span>
                            </a>
                        {/if}
                        {if isset($usuario_logado) and $bloquear_anexo}
                            <a class="dropdown-item btn-loading" href="{$app_url}solicitacao">
                                <i class="fa fa-exclamation-triangle"></i> Solicitações
                                <span class="badge badge-primary pull-right qtde_solicitacoes">{$qtde_solicitacoes}</span>
                            </a>
                        {/if}
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-envelope-open-o"></i> Remessa de Envio
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item btn-loading" href="{$app_url}remessa/buscar"><i
                                    class="fa fa-plus"></i> Nova</a>
                        <a class="dropdown-item btn-loading" href="{$app_url}remessa"><i
                                    class="fa fa-search"></i> Pesquisar</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-folder-open-o "></i> Arquivo Físico
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item btn-loading" href="{$app_url}localizacaoFisica/cadastrar"><i
                                    class="fa fa-plus"></i> Registrar</a>
                        <a class="dropdown-item btn-loading" href="{$app_url}localizacaoFisica"><i
                                    class="fa fa-search"></i>
                            Consultar</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-envelope-o "></i> Notificações
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item btn-loading" href="{$app_url}notificacao/cadastrar"><i
                                    class="fa fa-plus"></i> Nova</a>
                        <a class="dropdown-item btn-loading" href="{$app_url}notificacao"><i class="fa fa-th-list"></i>
                            Todas <span
                                    class="badge badge-primary pull-right">{$qtde_notificacoes_enviadas+$qtde_notificacoes_recebidas+$qtde_notificacoes_arquivadas}</span></a>
                        <a class="dropdown-item btn-loading" href="{$app_url}notificacao/index/enviadas"><i
                                    class="fa fa-send-o"></i> Enviadas <span
                                    class="badge badge-primary pull-right">{$qtde_notificacoes_enviadas}</span></a>
                        <a class="dropdown-item btn-loading" href="{$app_url}notificacao/index/recebidas"><i
                                    class="fa fa-envelope-open-o"></i> Recebidas <span
                                    class="badge badge-primary pull-right">{$qtde_notificacoes_recebidas}</span></a>
                        <a class="dropdown-item btn-loading" href="{$app_url}notificacao/index/arquivadas"><i
                                    class="fa fa-archive"></i> Arquivadas <span
                                    class="badge badge-primary pull-right">{$qtde_notificacoes_arquivadas}</span></a>
                    </div>
                </li>
                {if isset($app['lxsign']) && !empty($app['lxsign']) }
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <i class="fa  fa-pencil-square-o "></i> Assinaturas
                        </a>
                        <div class="dropdown-menu">
                            <a title="Visualizar Documentos pendente de assinatura" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/emProcesso"><i class="fa fa-clock-o"></i> Em Processo</a>
                            <a title="Visualizar Documentos Assinados" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/finalizados"><i class="fa fa-check"></i> Finalizados</a>
                            <div class="dropdown-divider"></div>
                            <a title="Gerenciar Signatários" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/signatarios"><i class="fa fa-address-card"></i> Signatários</a>
                            <a title="Gerenciar Grupos de Signatários" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/gruposSignarios"><i class="fa fa-users"></i> Grupos de Signatários</a>
                            <a title="Gerenciar Usuários" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/usuarios"><i class="fa fa-user"></i> Usuários</a>
                            <a title="Gerenciar Modelos de Documentos" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/modelosDocumento"><i class="fa fa-file-word-o"></i>  Modelos de Documentos</a>
                            <a title="Gerenciar Empresas/Secretarias" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/empresa"><i class="fa fa-briefcase"></i> Empresas/Secretarias</a>
                            <a title="Gerenciar Tipos de Documentos" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/tiposDocumento"><i class="fa fa-files-o"></i> Tipos Documentos</a>
                            <a title="Sair da Assinatura" class="dropdown-item btn-loading"
                               href="{$app_url}assinatura/sair"><i class="fa fa-files-o"></i> Sair</a>
                        </div>
                    </li>
                {/if}
                {$qtde_notificacoes=count($alertas)}
                <li class="nav-item dropdown">
                    <a id="dropdownNotificacoes" class="nav-link dropdown-toggle" href="#"
                       data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell text-warning {if $qtde_notificacoes gt 0}icon-animated-bell{/if}"></i>
                        <span class="badge badge-primary">{$qtde_notificacoes}</span>

                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownNotificacoes">
                        {if $qtde_notificacoes gt 0}
                            {foreach $alertas as $notificacao}
                                <a class="dropdown-item {$notificacao['classe']}"
                                   href="{$notificacao['href']}">{$notificacao['mensagem']}</a>
                            {/foreach}
                        {else}
                            <a class="dropdown-item" href="#">*Nenhuma notificação encontrada.</a>
                        {/if}
                    </div>
                </li>
            </ul>
        </div>
        <form class="form-inline pull-right p-2">
            <div class="form-group">
                <label class="mr-2 ml-2" for="select_exercicio">Exercício:</label>
                <select id="select_exercicio" class="form-control form-control-sm bg-warning" name="exercicio">
                    <option value="todos">Todos</option>
                    {foreach $anos as $ano}
                        <option value="{$ano}" {if $ano eq $exercicio_atual}selected{/if}>{$ano}</option>
                    {/foreach}
                </select>
            </div>
        </form>
    </div>
</nav>
