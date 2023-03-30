{if isset($lxsign_url)}
    <p id="lxsign_url" class="hidden">{$lxsign_url}</p>
    <p id="access_token" class="hidden">{$access_token}</p>
{/if}
<p id="access_token" class="hidden">{$access_token}</p>
{if !$anexo->getNumero() || !$anexo->getExercicio()}
<div class="alert alert-danger" role="alert" id="assinatura-alerta">
    Para enviar o documento para a assinatura digital é necessário cadastrar o número e exercício.
</div>
{/if}
{if !empty($assinatura) }
    {if $assinatura->getLxsign_id()}
        <div class="">
            <div class="row">
                <div class="col text-center">
                    Assinatura(s)<br>
                    <span>
                        <a target="_blanck" href="{$app_url}assinatura/visualizar/{$assinatura->getLxsign_id()}">Clique aqui para visualizar o documento no sistema de assinatura.</a>
                    </span>
                </div>
            </div>
            <div class="row align-items-center" style="margin-bottom: .5em">
                <div class="col-8">
                    <span>
                        <b>Data de envio:</b> {$assinatura->getDataCadastro()->format("d/m/Y")}
                    </span>
                </div>
                <div class="col-4">
                    <button id="btn_add_signatario" name="btn_add_signatario" class="btn btn-primary float-right small">Adicionar Signatário</button>
                </div>
            </div>
            <table id="table_signatarios" name="table_signatarios" class="table table-bordered">
                <thead>
                <tr>
                    <th class="text-center">Nome</th>
                    <th class="text-center">Status</th>
                </tr>
                </thead>
                <tbody>
                {foreach $consulta_assinatura->document->signatures as $a}
                    <tr>
                        <td>{$a->signer}</td>
                        <td class="text-center">{$a->status}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
            {include file="./formulario_signatario.tpl"}
        </div>
    {/if}
{/if}
<fieldset >

<form id="assinaturaForm" class="form-horizontal " method="POST" action="{$app_url}assinatura/{$acao}">
    <input type="hidden" name="anexo_id" value="{$anexo->getId()}"/>
    <input type="hidden" name="processo_id" value="{$anexo->getProcesso()->getId()}"/>
    {if empty($anexo->getId())}
        <input type="hidden" name="preenvio" value="1"/>
    {/if}
    <input type="hidden" name="anexo_indice" value="{$anexo_indice}"/>
    <input type="hidden" name="ajax" value="1"/>
    {if !empty($assinatura) }
        <input type="hidden" name="assinatura_id" value="{$assinatura->getId()}" />
    {/if}
    <div class="form-group">
        <label class="col-form-label">Grupos de Signatários:</label>
        <select name="grupo" class="select2 form-control" multiple="multiple">
            <option value="">Selecione</option>
            {foreach $grupos as $grupo}
                <option value="{$grupo->id}"  data-toggle="tooltip" title="{foreach $grupo->signatarios as $signatario} - {$signatario->nome} ({$signatario->cargo})&#010; {/foreach}"
                        {if $assinatura && $assinatura->getGrupoAsArray()->contains($grupo->id)}
                            selected="selected"
                        {/if}
                        data-signatarios="{$grupo->signatariosIds}"
                >
                        {$grupo->nome}
                </option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label class="col-form-label required">Signatários:</label>
        <select name="signatario" required class="select2 form-control" multiple="multiple" required>
            <option value="">Selecione</option>
            {foreach $signatarios as $signatario}
                <option value="{$signatario->id}"  data-toggle="tooltip" title="{$signatario->nome} - {$signatario->cargo}"
                        {if $assinatura && $assinatura->getSignatariosAsArray()->contains($signatario->id)}
                    selected="selected"
                        {/if}>
                    {$signatario->nome}
                </option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label class="col-form-label required">Tipo de Documento:</label>
        <select name="tipo_documento" class="select2 form-control tipo_documento_assinatura" required>
            <option value="">Selecione</option>
            {foreach $tipos_documentos as $item}
                <option value="{$item->id}"  data-toggle="tooltip"
                        {if $assinatura && $assinatura->getTipoDocumento() === $item->id}
                            selected="selected"
                        {/if}
                >
                    {$item->nome}
                </option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label class="col-form-label required">Empresa:</label>
        <select name="empresa" class="select2 form-control" required>
            <option value="">Selecione</option>
            {foreach $empresas as $item}
                <option value="{$item->id}"  data-toggle="tooltip"
                        {if $assinatura && $assinatura->getEmpresa() === $item->id}
                            selected="selected"
                        {/if}
                >
                    {$item->nome}
                </option>
            {/foreach}
        </select>
    </div>

    <div class="form-group row">
        <div class="col">
            <div style="display: flex;justify-content: space-between">
                <label class="col-form-label required">Número Doc.:</label>
                <span class="col-form-label">
                    Autonumerar?
                    <input type="checkbox"
                           id="auto_numero_doc"
                           name="auto_numero_doc"
                           value="1"
                           checked
                           onchange="alternarEntradaNumeroAnexo(this,'numero_documento_assinatura')"
                    />
                </span>
            </div>
            <input type="number" disabled class="form-control" max="2147483647" maxlength="10" name="numero" id="numero_documento_assinatura" value="{$numero}" {*{if !empty($numero)} disabled="true" {/if}*}  required="true"/>
        </div>
        <div class="col">
            <label class="col-form-label required">Exercício Doc.:</label>
            <br>
            <input type="number" class="form-control" name="exercicio" max="2099" min="1900" value="{$exercicio}" {*{if !empty($exercicio)} disabled="true" {/if}*}	required="true"/>
        </div>
        <div class="col">
            <label class="col-form-label required">Data Limite Assinatura:</label>
            <br>
        <input type="text" autocomplete="off" class="form-control datepicker" name="data_limite_assinatura"  value="{if $assinatura && $assinatura->getDataLimiteAssinatura()} {$assinatura->getDataLimiteAssinatura()->format("d/m/Y")} {else} {date("d-m-Y", strtotime('tomorrow'))} {/if}" required="true"/>
        </div>
    </div>    
    
    <hr/>
    <div class="form-group">
        {if !empty($assinatura) && !empty($assinatura->getId())}
            <a href="javascript:" url="{$app_url}assinatura/reenviarParaAssinatura/{$assinatura->getId()}"  class="btn btn-warning btn-reenviar-assinatura"><i class="fa fa-refresh"></i> Reenviar </a>
        {else}
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
        {/if}
        {if !isset($modal) || $modal eq false}
            <a href="{$app_url}assunto" class="btn btn-light border"><i class="fa fa-times"></i> Cancelar</a>
        {else}
            <a href="javascript:" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i class="fa fa-times"></i> Cancelar</a>
        {/if}
    </div>
</form>
</fieldset>

<script type="text/javascript" src="{$app_url}assets/js/view/assinatura/formulario.js?v={$file_version}"></script>
