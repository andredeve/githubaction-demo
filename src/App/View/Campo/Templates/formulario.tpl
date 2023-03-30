{include file="../../Public/Templates/progress_bar.tpl"}
<form id="formCampo" class="form-horizontal" method="POST" action="{$app_url}campo/{$acao}"
      enctype="multipart/form-data">
    <input type="hidden" name="id" value="{$campo->getId()}"/>
    <input type="hidden" name="ajax" value="true"/>
    <input type="hidden" name="acao" value="{$acao}"/>
    <input type="hidden" name="ordem" value="{$campo->getOrdem()}"/>
    <input type="hidden" name="setor_fase_id" value="{$campo->getSetorFase()->getId()}"/>
    <div class="form-group">
        <label class="col-form-label">Este campo é obrigatório?</label><br/>
        <div class="form-check form-check-inline">
            <label class="custom-control custom-radio">
                <input id="isObrigatorioSim" name="isObrigatorio" value="1" type="radio" class="custom-control-input"
                       {if $campo->getIsObrigatorio() eq true}checked{/if}>
                <label class="custom-control-label" for="isObrigatorioSim">Sim</label>
            </label>
        </div>
        <div class="form-check form-check-inline">
            <label class="custom-control custom-radio">
                <input id="isObrigatorioNao" name="isObrigatorio" value="0" type="radio" class="custom-control-input"
                       {if $campo->getIsObrigatorio() eq false}checked{/if} >
                <label class="custom-control-label" for="isObrigatorioNao">Não</label>
            </label>
        </div>
    </div>
    {*<div class="form-group">
    <label class="col-form-label">Seção:</label>
    <input class="form-control input-with-limit" type="text" value="{$campo->getSecao()}" maxlength="100" name="secao"/>
    <span class="form-text text-muted caracteres-restantes">Texto que agrupa campos de uma mesma seção para organizá-los.</span>
    </div>*}
    <div class="form-group">
        <label class="col-form-label required">Nome:</label>
        <input class="form-control input-with-limit" type="text" value="{$campo->getNome()}" maxlength="50"
               placeholder="Digite aqui o nome do campo" name="nome" required="true"/>
        <span class="form-text text-muted caracteres-restantes"></span>
    </div>
    <div class="form-group">
        <label class="col-form-label">Descrição:</label>
        <textarea class="form-control input-with-limit" maxlength="255" placeholder="Digite aqui a descrição do campo"
                  name="descricao">{$campo->getDescricao()}</textarea>
        <span class="form-text text-muted caracteres-restantes"></span>
    </div>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Tipo:</label>
            <div>
                <select id="select_tipo_campo" name="tipo" class="form-control form-control-sm" required="true">
                    <option value="">Selecione</option>
                    {foreach \App\Enum\TipoCampo::getOptions() as $value=>$text}
                        <option value="{$value}" {if $campo->getTipo() eq $value}selected{/if}>{$text}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="col">
            <label class="col-form-label">Máscara:</label>
            <div>
                <select id="select_mascara_campo" name="mascara" class="form-control form-control-sm">
                    <option value="" class="nenhuma">Nenhuma</option>
                    {foreach \App\Enum\MascaraCampo::getOptions() as $tipo_campo}
                        <option style="display: {if $campo->getTipo() neq $tipo_campo['class']}none{else}block{/if}"
                                class="{$tipo_campo['class']}" value="{$tipo_campo['value']}"
                                {if $campo->getMascara() eq $tipo_campo['value']}selected{/if}>{$tipo_campo['text']}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
    <div id="divArquivosMultiplos" style="{if $campo->getTipo() neq App\Enum\TipoCampo::ARQUIVO_MULTIPLO}display: none{/if}">
        <div class="form-group">
            <label class="col-form-label">Tipo documento:</label>
            <select id="select_tipo_documento" name="tipoTemplateMultiplosArquivos" class="form-control select2" required="true">
                <option value="">Selecione</option>
                {foreach $tipos_documento as $tipo}
                    <option value="{$tipo->getId()}"
                            {if $campo->getTipoTemplate() neq null and $tipo->getId() eq $campo->getTipoTemplate()->getId()}selected{/if}>{$tipo->getDescricao()}</option>
                {/foreach}
            </select>
            <small class="form-text text-muted">* Defina um tipo de documento que será anexado.</small>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="assinaturaObrigatoria"
                       id="customCheckAssinaturaObrigatoria" onchange="validarObrigatorio(this)"
                       {if $campo->getAssinaturaObrigatoria() eq true}checked{/if}>
                <label class="custom-control-label" for="customCheckAssinaturaObrigatoria">
                    Marque caso assinatura digital seja obrigatória. (Não aplicável para contribuinte).
                </label>
            </div>
        </div>
    </div>
    <div id="divTemplate" style="{if $campo->getTipo() neq App\Enum\TipoCampo::ARQUIVO}display: none{/if}">
        <div class="form-group">
            <label class="col-form-label">Tipo documento:</label>
            <select id="select_tipo_documento" name="tipoTemplate" class="form-control select2" required="true">
                <option value="">Selecione</option>
                {foreach $tipos_documento as $tipo}
                    <option value="{$tipo->getId()}"
                            {if $campo->getTipoTemplate() neq null and $tipo->getId() eq $campo->getTipoTemplate()->getId()}selected{/if}>{$tipo->getDescricao()}</option>
                {/foreach}
            </select>
            <small class="form-text text-muted">* Defina um tipo de documento que será anexado.</small>
        </div>
        <div class="form-group">
            <label class="col-form-label">
                Template (opcional):
            </label>
            {if $campo->getTemplate() neq null}
                <a title="Ver template selecionado" class="btn btn-link btn-sm pull-right" target="_blank"
                   href="{$app_url}_files/processos/templates/{$campo->getTemplate()}"><i class="fa fa-paperclip"></i>
                    Visualizar Template</a>
            {/if}
            <input accept=".doc,.docx" type="file" class="form-control-file form-control-sm fileinput" name="template"/>
            <small class="form-text text-muted">
                * Insira um template do word para o arquivo a ser anexado no processo. (Não aplicável para contribuinte)
            </small>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" onchange="validarObrigatorio(this)" name="numeroTemplateObrigatorio"
                       id="customCheckObrigatorioNumero"
                       {if $campo->getNumeroTemplateObrigatorio() eq true}checked{/if}>
                <label class="custom-control-label" for="customCheckObrigatorioNumero">
                    Marque caso o preenchimento do número do documento seja obrigatório.
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="assinaturaObrigatoria"
                       id="customCheckAssinaturaObrigatoria" onchange="validarObrigatorio(this)"
                       {if $campo->getAssinaturaObrigatoria() eq true}checked{/if}>
                <label class="custom-control-label" for="customCheckAssinaturaObrigatoria">
                    Marque caso assinatura digital seja obrigatória. (Não aplicável para contribuinte).
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="circulacaoInterna"
                       id="customCheckCirculacaoInterna" onchange="validarObrigatorio(this)"
                       {if $campo->getCirculacaoInterna() eq true}checked{/if}>
                <label class="custom-control-label" for="customCheckCirculacaoInterna">
                    Marque caso o documento seja circulação interna (Não irá aparecer no {$nomenclatura} Externo).
                </label>
            </div>
        </div>
    </div>
    <div id="divValoresSelecao" class="form-group"
         style="{if $campo->getTipo() neq App\Enum\TipoCampo::CAIXA_SELECAO}display: none{/if}">
        <label class="col-form-label required">Valores da Seleção:</label>
        <select name="valoresSelecao[]" class="form-control select2_tag" multiple="true" required="true">
            {if $campo->getValoresSelecao() neq null}
                {$valores=explode(";",$campo->getValoresSelecao())}
                {foreach $valores as $valor}
                    <option value="{$valor}" selected>{$valor}</option>
                {/foreach}
            {/if}
        </select>
        <small class="form-text text-muted">Digite os valores da caixa de seleção.</small>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i>
            Salvar
        </button>
        <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                    class="fa fa-times"></i> Cancelar</a>
    </div>
</form>
<script defer src="{$app_url}assets/js/view/campo/formulario.js"></script>



