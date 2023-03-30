<p class="lead"><i class="fa fa-file-o"></i> {$nomenclatura} {$processo->getNumero()}
    /{$processo->getExercicio()}
    {if \App\Enum\SigiloProcesso::SEM_RESTRICAO !=  $processo->getSigilo()}
        <span class="badge badge-danger"><i class="fa fa-lock"> {\App\Enum\SigiloProcesso::getOptions($processo->getSigilo())}</i></span>
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
<table class="table table-sm table-bordered table-striped">
    <tr>
        <th class="w-25">Origem:</th>
        <td>{\App\Enum\OrigemProcesso::getDescricao($processo->getOrigem())}</td>
    </tr>
    <tr>
        <th class="w-25">Sigilo:</th>
        <td>{\App\Enum\SigiloProcesso::getOptions($processo->getSigilo())}</td>
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
        <td>{$processo->getDataVencimentoAtualizada(true)}</td>
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
                <a class="nav-link active" data-toggle="tab" href="#vencimentosTabView{$processo->getId()}" role="tab"
                   aria-controls="vencimentosTabView{$processo->getId()}"
                   aria-selected="false"><i class="fa fa-clock-o"></i> Vencimentos <span id="qtde_documentos_processo"
                                                                                         class="badge badge-primary">{count($processo->getDocumentos())}</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#anexosTabView{$processo->getId()}" role="tab"
                   aria-controls="anexosTabView{$processo->getId()}"
                   aria-selected="false"><i class="fa fa-paperclip"></i> Anexos <span id="qtde_anexos_processo"
                                                                                      class="badge badge-primary">{count($processo->getAnexos())}</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#apensosTabView{$processo->getId()}" role="tab"
                   aria-controls="apensosTabView{$processo->getId()}"
                   aria-selected="false"><i class="fa fa-files-o"></i> Apensos <span
                            id="qtde_processo_vinculados"
                            class="badge badge-primary">{count($processo->getApensos())}</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tramitesTabView{$processo->getId()}" role="tab"
                   aria-controls="tramitesTabView"
                   aria-selected="false"><i class="fa fa-history"></i> Trâmites <span id="qtde_tramites"
                                                                                      class="badge badge-primary">{count($processo->getTramites())}</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#localizacaoFisicaTabView{$processo->getId()}" role="tab"
                   aria-controls="localizacaoFisicaTabView{$processo->getId()}"
                   aria-selected="false"><i class="fa fa-folder-open-o"></i> Localização </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#historicoTabView{$processo->getId()}" role="tab"
                   aria-controls="historicoTabView{$processo->getId()}" aria-selected="false"><i
                            class="fa fa-history"></i> Histórico</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        {$consultar=true}
        <div class="tab-content">
            <div class="tab-pane active" id="vencimentosTabView{$processo->getId()}" role="tabpanel">
                {$documentos=$processo->getDocumentos()}
                {include file="../../Documento/Templates/listar.tpl"}
            </div>
            <div class="tab-pane" id="anexosTabView{$processo->getId()}" role="tabpanel">
                {if $processo->usuarioTemPermissaoAnexo() }
                    {include file="../../Anexo/Templates/listar.tpl"}
                {else}
                    {include file="../../Public/Templates/sem_acesso_sigiloso.tpl"}
                {/if}
            </div>
            <div class="tab-pane" id="apensosTabView{$processo->getId()}" role="tabpanel">
                {$resultado=$processo->getApensos()->toArray()}
                {include file="listar.tpl"}
            </div>
            <div class="tab-pane" id="tramitesTabView{$processo->getId()}" role="tabpanel">
                {include file="../../Tramite/Templates/listar.tpl"}
            </div>
            <div class="tab-pane" id="localizacaoFisicaTabView{$processo->getId()}" role="tabpanel">
                {$disabled_localizacao=true}
                {include file="../../Processo/Templates/localizacao.tpl"}
            </div>
            <div class="tab-pane" id="historicoTabView{$processo->getId()}" role="tabpanel">
                {include file="../../Processo/Templates/historico.tpl"}
            </div>
        </div>
    </div>
</div>
<hr/>
<div class="text-right">
    {if $processo->getSigilo() != \App\Enum\SigiloProcesso::SIGILOSO}
        <a class="btn btn-warning btn-processo-digital"
           title="Visualizar Processo Digital"
           href="javascript:"
           processo_id="{$processo->getId()}"
           href-relatorio="{$app_url}src/App/View/Processo/visualizar_digital.php?processo_id={$processo->getId()}">
            <i class="fa fa-search"></i> {$nomenclatura} Digital
        </a>
    {/if}
    <a title="Editar {$nomenclatura}"
       href="{$app_url}processo/editar/id/{$processo->getId()}"
       class="btn btn-info btn-loading">
        <i class="fa fa-edit"></i> Editar
    </a>
</div>