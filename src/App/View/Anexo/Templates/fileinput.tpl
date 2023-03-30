{**********************************}
{***Última Alteração: 03/02/2023***}
{*************André****************}
{**********************************}
<div id="file-input">
    <div class="file-loading">
        <input id="arquivo_processo" {if isset($disabled) and $disabled} disabled="true" {/if}preview="{$anexo->getPreview()}"
               name="arquivo_processo" type="file"
               data-msg-placeholder="Selecione um arquivo para anexar...">
    </div>
    <input type="hidden" name="arquivo_processo" value="{$anexo->getArquivo()}"/>
    <small class="form-text text-muted">* Tamanho máximo arquivo: {ini_get('post_max_size')}</small>
    <small class="form-text text-muted">* Extensões permitidas: pdf, jpg, jpeg, gif e png.</small>
    <div id="kartik-file-errors"></div>
</div>
<!-- Editor de Modelo -->
<div id="model-input" class="hidden">
    <div class="card">
        <div class="card-body p-2">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-transparent text-primary"><i
                                    class="fa fa-file"></i></div>
                    </div>
                    <select class="custom-select documentModelInput" name="modelo_id" id="selectModelo">
                        {if $acao  eq 'atualizar'}
                        <option value="{if isset($anexo->getId())}{$anexo->getId()}{else}AtualizarModeloTemp{/if}">Modelo Atual</option>
                        {else}
                            <option value="">Selecione Modelo</option>
                        {/if}
                        {foreach $modelos as $modelo}
                            <option value="{$modelo->getId()}">{$modelo->getNome()}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            {if !isset($anexo->getId())}
              <input type="hidden" id="texto_temp" data-texto='{$anexo->getTextoOCR()}' />
            {/if}
            <!-- TinyMCE -->
            <textarea id="editor_estrutura" class="form-control editor" height="600" name="texto">                  
                {if $acao  eq 'atualizar'}
                   {$anexo->getTextoOCR()}
                {/if}
            </textarea>
        </div>
    </div>
</div>