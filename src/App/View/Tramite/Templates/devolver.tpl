{if  $tramite->getAssunto()->getFluxograma() eq null or $tramite->getForaFluxograma() eq false or $tramite->getCancelouDecisao() eq true}
    <form id="devolverProcessoForm" method="POST" action="{$app_url}processo/devolver">
        <input type="hidden" name="tramite_id" value="{$tramite->getId()}"/>
        <input type="hidden" name="devolver" value="1">
        <input type="hidden" name="fora_fluxograma" value="1">
        <input type="hidden" name="setor_destino_id[0]" value="{$tramite->getSetorAnterior()->getId()}"/>
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
            <tr>
                <th>Responsável</th>
                <th>Setor Atual</th>
                <th>Devolver para</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{$tramite->getResponsavel()}</td>
                <td>{$tramite->getSetorAtual()}</td>
                <td>{$tramite->getSetorAnterior()}</td>
            </tr>
            </tbody>
        </table>
        <div class="form-group row">
            <div class="col" {if $tramite->getProcesso()->getIsExterno()}style="display: none;" {/if}>
                <label>Usuário Destino:</label>
                <select name="usuario_destino_id[0]" class="form-control form-control-sm usuario_destino_processo">
                    <option value="">Todos</option>
                    {foreach $tramite->getSetorAnterior()->getUsuarios() as $usuario}
                        <option value="{$usuario->getId()}">{$usuario->getPessoa()->getNome()}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col">
                <label>Status:</label>
                <select id="select_status_processo" name="status_processo_id[0]" class="form-control form-control-sm"
                        required readonly tabindex="-1" style="pointer-events: none; cursor: not-allowed;">
                    <option value="">Selecione</option>
                    {foreach $status_processo as $status}
                        <option is_arquivamento="{$status->getIsArquivamento()}"
                                value="{$status->getId()}" {if $status->getDescricao() eq "EM ANDAMENTO"}selected{/if}>{$status->getDescricao()}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Parecer/Motivo devolução:</label>
            <textarea name="descricao_tramite[0]" class="form-control" rows="4" required></textarea>
            <small class="form-text text-muted">Descreva o motivo da devolução.</small>
        </div>
        {if $tramite->getProcesso()->getIsExterno()}
            <div class="divRequisitosProximoTramite">
                <hr>
                {include file="../../Tramite/Templates/documento_requerido.tpl"}
            </div>
        {/if}
        <hr/>
        <div class="form-group text-right">
            <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button">
                <i class="fa fa-reply"></i>
                Recusar
            </button>
            {*<a href="#" class="btn btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                        class="fa fa-times"></i> Cancelar</a>*}
        </div>
    </form>
    <script defer type="text/javascript" src="{$app_url}assets/js/view/tramite/tramitar.js?v={$file_version}"></script>
{else}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <h3><font face="Tahoma" color="red"><i class="fa fa-ban"></i> Aviso</font>
                    <small>: devolução não permitida.</small>
                </h3>
                <br/>
                <p class="lead">
                    {if $tramite->getSetorAnterior() eq null}
                        Desculpe, esse {$nomenclatura} não possui setor anterior em seu trâmite para ser devolvido.
                    {else}
                        Desculpe, mas esse {$nomenclatura} já se encontra devolvido. Para prosseguir, é
                        necessário encaminhá-lo pela opção "Tramitar".
                    {/if}
                </p>
            </div>
        </div>
    </div>
{/if}