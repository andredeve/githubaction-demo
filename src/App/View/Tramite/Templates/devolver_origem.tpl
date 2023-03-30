{*{if  $tramite->getAssunto()->getFluxograma() eq null or $tramite->getForaFluxograma() eq false or $tramite->getCancelouDecisao() eq true}*}
    <form id="devolverProcessoForm" method="POST" action="{$app_url}processo/devolver">
        <input type="hidden" name="tramite_id" value="{$tramite->getId()}"/>
        <input type="hidden" name="devolver" value="1">
        <input type="hidden" name="fora_fluxograma" value="1">
        <input type="hidden" name="setor_destino_id[0]" value="{$tramite->getProcesso()->getSetorOrigem()->getId()}"/>
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
                <td>{$tramite->getProcesso()->getSetorOrigem()}</td>
            </tr>
            </tbody>
        </table>
        <div class="form-group row" {if $tramite->getProcesso()->getIsExterno()}style="display: none;" {/if}>
            <div class="col">
                <label>Usuário Destino:</label>
                <select name="usuario_destino_id[0]" class="form-control form-control-sm usuario_destino_processo">
                    <option value="">Todos</option>
                    {foreach $tramite->getProcesso()->getSetorOrigem()->getUsuarios() as $usuario}
                        <option value="{$usuario->getId()}">{$usuario->getPessoa()->getNome()}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col">
                <label>Status:</label>
                <select id="select_status_processo" name="status_processo_id[0]" class="form-control form-control-sm"
                        required>
                    
                    {foreach $status_processo as $status}
                        {if $status->getIsDevolvidoOrigem()}
                            <option is_arquivamento="{$status->getIsArquivamento()}"
                                value="{$status->getId()}">{$status->getDescricao()}</option>
                        {/if}
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
        <div class="form-group row">
            <div class="col">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="gerar_guia_tramitacao" value="1" class="custom-control-input"
                           id="customCheckGTE">
                    <label class="custom-control-label" for="customCheckGTE">Gerar Guia de Remessa de
                        Envio</label>
                </div>                
            </div>
        </div>

        <div class="form-group text-right">
            <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button">
                <i class="fa fa-reply"></i>
                Devolver à Origem
            </button>
        </div>
    </form>
<script defer type="text/javascript" src="{$app_url}assets/js/view/tramite/tramitar.js?v={$file_version}"></script>