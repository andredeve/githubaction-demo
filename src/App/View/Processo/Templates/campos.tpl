{**********************************}
{***Última Alteração: 07/02/2023***}
{*************André****************}
{if !$usuarioEhInteressado}
    {if $acao eq 'inserir' && $podeAlterarNumeroProcesso}
        <div class="form-group row">
            <div class="col-3">
                <label class="col-form-label ">Número: </label>
                <input type="number" name="numero" class="form-control form-control-sm" />
            </div>
            <div class="col-3">
                <label class="col-form-label ">Exercicio:</label>
                <input type="number" name="exercicio" class="form-control form-control-sm" />
            </div>
        </div>
    {/if}

    <div class="form-group row">
        <div class="col-3">
            <label class="col-form-label required">Origem:</label>
            <select name="origem" class="form-control form-control-sm" required>
                <option value="">Selecione</option>
                {foreach $origens as $value=>$text}
                    <option value="{$value}" {if $processo->getOrigem() eq $value}selected{/if}>{$text}</option>
                {/foreach}
            </select>
        </div>
        <div class="col-3">
            <label class="col-form-label">Sigilo:
                <a href="javascript:" data-toggle="tooltip-html" title="{foreach \App\Enum\SigiloProcesso::getOptionsDescription() as $descricao}{$descricao}<br>{/foreach}"><i class="fa fa-question-circle" ></i></a>
            </label><br/>
            <select name="sigilo" class="form-control form-control-sm">
                {foreach \App\Enum\SigiloProcesso::getOptions() as $id => $text}
                    <option value="{$id}" {if $processo->getSigilo() == $id} selected {/if} >{$text}</option>
                {/foreach}
            </select>
        </div>
        <div class="col">
            <label class="col-form-label required">Setor Origem:</label>
            {if $acao eq 'inserir'}
                <div class="float-right">
                    <a href="#" entidade="Setor"
                       title="Pesquisa avançada por Setor"
                       class="btn btn-xs btn-info btn-selectionar-entidade"><i
                                class="fa fa-search"></i></a>
                    <a id="cadastrar:Setor"
                       href="#" href_select="select_setor_origem"
                       title="Cadastrar novo Setor"
                       class="btn btn-xs btn-success btn-cadastrar-modal"><i
                                class="fa fa-plus"></i></a>
                </div>
            {/if}
            <select data-placeholder="Selecione" id="select_setor_origem" {if $acao eq 'atualizar' and (!isset($podeEditarSetorOrigem) || !$podeEditarSetorOrigem)}disabled="true"{/if}
                    class="form-control form-control-sm select2"
                    name="setor_origem_id" required="true">
                <option value="">Selecione</option>
                {$qtde_setores=count($setores)}

                {foreach $setores as $setor}
                    <option value="{$setor->getId()}"
                            {if $processo->getSetorOrigem()->getId() eq $setor->getId() or $qtde_setores eq 1}selected{/if}>{$setor->getNome()}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div id="users-permission" class="form-group row" {if $processo->getSigilo() neq 'sigiloso'}style="display: none;"{/if}>
        <div class="col">
            <label class="col-form-label">Usuários permitidos:</label>
            <div class="float-right">
                <a href="#"
                   title="Pesquisa avançada por usuário"
                   class="btn btn-xs btn-info btn-pesquisar-usuario"><i
                            class="fa fa-search"></i></a>
            </div>
            <select id="select_usuarios_permitidos" name="usuariosPermitidos[]" multiple="true"
                data-app-action="{$app_url}usuario/buscar">
                {foreach $processo->getUsuariosPermitidos() as $permitido}
                    <option value="{$permitido->getId()}" selected>{$permitido->getPessoa()->getNome()}</option>
                {/foreach}
            </select>
        </div>
    </div>
{else}
    <input type="hidden" id="interessado_id" name="interessado_id"/>
{/if}
<div class="form-group row">
    <div class="{if $usuarioEhInteressado}col-6{else}col-3{/if}">
        <label class="col-form-label required">Assunto:</label>
        {if $acao eq 'inserir'}
            <div class="float-right">
                <a href="#" entidade="Assunto"
                   title="Pesquisa avançada por Assunto"
                   class="btn btn-xs btn-info btn-selectionar-entidade">
                    <i class="fa fa-search"></i>
                </a>
                {if !$usuarioEhInteressado}
                    <a id="cadastrar:Assunto"
                       href="#" href_select="select_assunto"
                       title="Cadastrar novo Assunto"
                       class="btn btn-xs btn-success btn-cadastrar-modal">
                        <i class="fa fa-plus"></i>
                    </a>
                {/if}
            </div>
        {/if}
        <select
            id="select_assunto"
            data-placeholder="Selecione"
            class="form-control select_assunto"
            {if $acao eq 'atualizar'}disabled="true"{/if}
            name="assunto_id"
            required="true"       >
            <option value="" selected>Selecione</option>
            {if $processo->getAssunto()->getId() neq null}
                <option value="{$processo->getAssunto()->getId()}" selected>{$processo->getAssunto(true)}</option>
            {/if}
        </select>
    </div>
    {if $usuarioEhInteressado and $acao eq 'atualizar'}
        <div class="{if $usuarioEhInteressado}col-6{else}col-3{/if}">
            <label class="col-form-label required">Setor atual: </label>
            <input type="text" class="form-control" disabled value="{$processo->getSetorAtual()}"/>
        </div>
    {/if}
    {if !$usuarioEhInteressado}
        <div class="col-3">
            <label class="col-form-label required">Interessado:</label>
            <div class="float-right">
                <a href="#"
                   title="Pesquisa avançada por Interessado"
                   class="btn btn-xs btn-info btn-pesquisar-interessado"><i
                            class="fa fa-search"></i></a>
                <a id="cadastrar:Interessado:modal-lg"
                   href="#" href_select="select_interessado"
                   title="Cadastrar novo Interessado"
                   class="btn btn-xs btn-success btn-cadastrar-interessado"><i
                            class="fa fa-plus"></i></a>
            </div>
            <select id="select_interessado" class="form-control select_interessado" name="interessado_id" required="true">
                <option value=""></option>
                {if $processo->getInteressado()->getId() neq null}
                    <option value="{$processo->getInteressado()->getId()}"
                            selected>{$processo->getInteressado()}</option>
                {/if}
            </select>
        </div>
        <div class="col-2">
            <label class="col-form-label required">Data:</label>
            <input id="data_abertura" data_fim_id="data_vencimento" type="text" name="dataAbertura"
                   value="{$processo->getDataAbertura()->format('d/m/Y')}"  {if $acao eq 'atualizar'}disabled="true"{/if}
                   class="form-control form-control-sm data date-range data_abertura" required="true">
        </div>

        <div class="col-2">
            <label class="col-form-label required">Vencimento:</label>

            <input id="data_vencimento" type="text" name="dataVencimento"
                   value="{if $processo->getDataVencimento() neq null}{$processo->getDataVencimento()->format('d/m/Y')}{/if}"
                   {if $acao eq 'atualizar'}disabled="true"{/if}
                   class="form-control form-control-sm data" required="true">
        </div>
        {if $acao eq 'atualizar'}
            <div class="col-2">
                <label class="col-form-label required">Vigência:</label>

                <input id="data_vencimento" type="text" name="dataVencimento"
                       value="{if $processo->getDataVencimento() neq null}{$processo->getDataVencimentoAtualizada(true)}{/if}"
                       {if $acao eq 'atualizar'}disabled="true"{/if}
                       class="form-control form-control-sm data" required="true">
            </div>
        {/if}
    {/if}
</div>
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#processoTab" role="tab"
                   aria-controls="processoTab"
                   aria-selected="true">
                    <i class="fa fa-info-circle"></i> {if $usuarioEhInteressado}Detalhe da Solicitação{else}Objeto/requerimento{/if}
                </a>
            </li>
            {if !$usuarioEhInteressado}
                {if $acao eq 'inserir' or $acao eq 'atualizar'}
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#apensosTab" role="tab" aria-controls="apensosTab" aria-selected="false">
                            <i class="fa fa-files-o"></i> Apensos
                            {if !$processo->getApensado()}
                                <span id="qtde_apensos" class="badge badge-primary">{$processo->getApensos()->count()}</span>
                            {/if}
                            {if $processo->getApensado()}
                                <span id="processo_apensado" class="badge badge-warning">
                                    <i class="fa fa-exclamation"></i>
                                </span>
                            {/if}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#vencimentosTab" role="tab" aria-controls="vencimentosTab" aria-selected="false">
                            <i class="fa fa-clock-o"></i> Vencimentos
                        </a>
                    </li>
                {/if}
            {/if}
            {if $acao eq 'atualizar'}
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabDocumentos" role="tab" aria-controls="tabDocumentos" aria-selected="false">
                        <i class="fa fa-paperclip"></i> Documentos Anexos
                        <span id="qtde_anexos_processo" class="badge badge-primary">{count($processo->getAnexos())}</span>
                    </a>
                </li>
            {/if}
            {if !$usuarioEhInteressado}
                {if $acao eq 'atualizar'}
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#historicoTramitesTab" role="tab" aria-controls="historicoTramitesTab" aria-selected="false">
                            <i class="fa fa-history"></i> Trâmites
                            <span id="qtde_tramites_processo" class="badge badge-primary">{$processo->getTramites()->count()}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#localizacaoFisicaTab" role="tab" aria-controls="localizacaoFisicaTab" aria-selected="false">
                            <i class="fa fa-folder-open-o"></i> Localização Física
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#historicoProcessoTab" role="tab" aria-controls="historicoProcessoTab" aria-selected="false">
                            <i class="fa fa-history"></i>Histórico
                        </a>
                    </li>
                {/if}
            {/if}
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane active" id="processoTab" role="tabpanel">
                <div class="form-group">
                    <textarea name="objeto" {if count($processo->getTramites()) > 1 and !$usuario->isAdm()}disabled{/if} class="form-control maiscula" required="true" rows="5">{$processo->getObjeto()}</textarea>
                </div>
                {if !$usuarioEhInteressado}
                    <small class="form-text text-muted"><strong>Aviso:</strong> ao gerar etiqueta, o sistema só imprimirá as
                        três (3) primeiras linhas com 47 caracteres cada.
                    </small>
                {else}
                    <small class="form-text text-muted">
                        <strong>Observação:</strong>
                        Informe brevemente detalhes da sua solicitação.
                    </small>
                {/if}
            </div>
            {if $acao eq 'inserir' or $acao eq 'atualizar'}
                <div class="tab-pane" id="apensosTab" role="tabpanel">
                    {if $processo->getApensado() neq null}
                        <p class="lead">Apensado ao {$parametros['nomenclatura']}:
                            <a href="#" title="Visualizar {$parametros['nomenclatura']}" class="btn btn-link"
                               onclick="visualizarProcesso({$processo->getApensado()->getId()})"><i
                                        class="fa fa-file-o"></i> {$processo->getApensado()}
                            </a>
                        </p>
                    {/if}
                    {if !$processo->getApensado()}
                        <div class="form-group">
                            <label class="col-form-label">{$parametros['nomenclatura']}s vinculados a este:</label>
                            <div class="float-right">
                                <a href="#" entidade="Processo"
                                title="Pesquisa avançada por {$parametros['nomenclatura']}"
                                class="btn btn-xs btn-info btn-selectionar-entidade" data-processoid="{$processo->getId()}" {if !empty($processo->getApensado())}data-apensadoid="{$processo->getApensado()->getId()}"{/if}><i
                                            class="fa fa-search"></i></a>
                                <a href="#" processo_id="{$processo->getId()}"
                                title="Cadastrar novo {$parametros['nomenclatura']} Apenso"
                                class="btn btn-xs btn-success btn-cadastrar-apenso"><i
                                            class="fa fa-plus"></i></a>
                            </div>
                            <select id="select_apensos" class="form-control select_processo"
                                    name="apensos_id[]" multiple="true">
                                <option></option>
                                {foreach $processo->getApensos() as $vinculado}
                                    <option value="{$vinculado->getId()}" selected>{$vinculado}</option>
                                {/foreach}
                            </select>
                            <small class="form-text text-muted">Selecione um ou mais processos que serão vinculados e
                                este.
                            </small>
                        </div>
                        <div id="divApensos">
                            {$resultado=$processo->getApensos()->toArray()}
                            {include file="../../Processo/Templates/listar.tpl"}
                        </div>
                    {else}
                        <div class="alert alert-danger" role="alert">
                                Não é possível apensar processos a este, pois ele está apensado ao {$parametros['nomenclatura']} nº {$processo->getApensado()}.<br><br>
                                Caso necessário, acesse o {$parametros['nomenclatura']} nº {$processo->getApensado()} e desapense-o para liberar essa funcionalidade.
                        </div>
                    {/if}
                </div>
                <div class="tab-pane" id="vencimentosTab" role="tabpanel">
                    {$documentos=$processo->getDocumentos()}
                    <div id="listaDocumento_{$processo->getId()}">
                        {include file="../../Documento/Templates/listar.tpl"}
                    </div>
                </div>
            {/if}
            {if $acao eq 'atualizar'}
                <div class="tab-pane" id="tabDocumentos" role="tabpanel">
                    {if !$usuarioEhInteressado}

                        <div class="text-right">
                            <a href="#" class="btn btn-success btn-cadastrar-anexo
                                {if $hasAttachAddPermission neq true} disabled {/if}">
                                <i class="fa fa-plus"></i>Novo
                            </a>
                            <button type="button" data-toggle="modal" data-target="#adc-anexos-modal"
                                    class="btn btn-success {if $hasAttachAddPermission neq true} disabled {/if}">
                                <i class="fa fa-plus"></i> Adicionar Múltiplos
                            </button>
                            {if isset($config["lxfiorilli"]) && !empty($config["lxfiorilli"])}
                                <a href="#" class="btn btn-warning btn-importar-anexo {if $hasAttachAddPermission neq true} disabled {/if}"">
                                    <i class="fa fa-download"></i> Importar
                                </a>
                            {/if}
                            <a processo_id="{$processo->getId()}" href="#" title="Mesclar anexos"
                               class="btn btn-info btn-mesclar-anexos {if $hasAttachAddPermission neq true} disabled {/if}">
                                <i class="fa fa-sitemap"></i> Mesclar
                            </a>
                            {if $hasAttachAddPermission neq true}
                                <p class="text-danger">* Permitido manipular anexos quando tramitado para o setor responsável.</p>
                            {/if}
                        </div>
                    {/if}
                    <hr/>
                    <div id="divAnexos">
                        {if $processo->usuarioTemPermissaoAnexo() }
                            {include file="../../Anexo/Templates/listar.tpl"}
                        {else}
                            {include file="../../Public/Templates/403.tpl"}
                        {/if}
                    </div>
                </div>
                <div class="tab-pane" id="historicoTramitesTab" role="tabpanel">
                    {include file="../../Tramite/Templates/listar.tpl"}
                </div>
                <div class="tab-pane" id="localizacaoFisicaTab" role="tabepanel">
                    {include file="../../Processo/Templates/localizacao.tpl"}
                </div>
                <div class="tab-pane" id="historicoProcessoTab" role="tabpanel">
                    {include file="../../Processo/Templates/historico.tpl"}
                </div>
            {/if}
        </div>
    </div>
</div>
<script defer="defer" src="{$app_url}assets/js/view/processo/permissoes.js"></script>
<script defer="defer" src="{$app_url}assets/js/view/processo/permissoes.js"></script>