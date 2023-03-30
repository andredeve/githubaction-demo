{if $modal eq true}
    <h4 class='modal-title-dinamic'>{$page_title}</h4>
{/if}
<form id="setorForm" class="form-horizontal form-validate" method="POST" action="{$app_url}setor/{$acao}">
    <input type="hidden" name="id" value="{$setor->getId()}"/>
    {*<input id="setor_pai_id" type="hidden" name="setor_pai_id" value="{$setor->getSetorPai()->getId()}"/>*}
    {if !isset($modal) ||  $modal eq false}
        <div class="form-group">
            <label class="col-form-label">Subt-Setor de:</label>
            <select name="setor_pai_id" class="select2Tree" placeholder="selecione">
                <option value=""></option>
                {include file="../../Setor/Templates/select.tpl" isCadastroSetores=true setor_sel=$setor->getSetorPai()->getId()}
            </select>
        </div>
        <div class="form-group row">
            <div class="col">
                <label class="col-form-label required">Setor:</label>
                <input type="text" autofocus="true" class="form-control form-control-sm maiscula"
                       value="{$setor->getNome()}"  name="nome" required="true">
            </div>
            <div class="col-1 divOrgao{if   $setor->getSetorPai()->getId()} d-none {/if}" >
                <label class="col-form-label not-required  " >Órgão:</label>
                <input type="text" class="form-control form-control-sm codigo-orgao"
                       value="{$setor->getOrgao()}" name="orgao" >
            </div>
            <div class="col-2 divUnidade {if !$setor->getSetorPai() || !$setor->getSetorPai()->getId()} d-none {/if}">
                <label class="col-form-label not-required " >Unidade:</label>
                <input type="text" class="form-control form-control-sm codigo-unidade"
                       name="unidade" value="{$setor->getUnidade()}" >
            </div>
            <div class="col-md-2">
                <label class="col-form-label">Sigla:</label>
                <input type="text" class="form-control form-control-sm maiscula" required value="{$setor->getSigla()}"
                       name="sigla">
            </div>
            <div class="col-md-2">
                <label class="col-form-label">Ativo?</label><br/>
                <div class="form-check form-check-inline">
                    <label class="custom-control custom-radio">
                        <input id="setorAtivo" name="isAtivo" value="1" type="radio" class="custom-control-input"
                               {if $setor->getIsAtivo() eq true}checked{/if}>
                        <label class="custom-control-label" for="setorAtivo">Sim</label>
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <label class="custom-control custom-radio">
                        <input id="setorInativo" name="isAtivo" value="0" type="radio" class="custom-control-input"
                               {if $setor->getIsAtivo() eq false}checked{/if}>
                        <label class="custom-control-label" for="setorInativo">Não</label>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="form-group row" >
            <div class="col-3">
                <label class="col-form-label">Externo?
                    <i data-toggle="popover" data-html="true" data-content="Quando essa opção estiver marcada o setor ficará disponível para interação com os interessados externos. " class="fa fa-question-circle text-info tooltip-icon"></i>
                </label><br/>
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-radio">
                        <input id="radioExterno1" type="radio" name="isExterno" value="1" class="custom-control-input" {if $setor->getIsExterno() eq true}checked{/if}>
                        <label class="custom-control-label" for="radioExterno1">Sim</label>
                    </div>
                </div>
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-radio">
                        <input id="radioExterno2" type="radio" name="isExterno" value="0" class="custom-control-input" {if $setor->getIsExterno() eq false}checked{/if}>
                        <label class="custom-control-label" for="radioExterno2">Não</label>
                    </div>
                </div>
            </div> 
        </div>   
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="arquivar" value="1" class="custom-control-input"
                       id="customCheckArquivar" {if $setor->getArquivar() eq true}checked{/if}>
                <label class="custom-control-label" for="customCheckArquivar">Marque para arquivar quando enviar para esse
                    setor.</label>
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="disponivel_tramite" value="1" class="custom-control-input"
                       id="customCheckDisponivelTramite" {if $setor->getDisponivelTramite() eq true}checked{/if}>
                <label class="custom-control-label" for="customCheckDisponivelTramite">
                    Marque caso o setor esteja disponível para trâmite.
                
                </label>
                {if count($setor->getUsuarios()) > 0}
                    <div class="alert alert-warning">
                        <strong> Atenção existe {count($setor->getUsuarios())} usuário(s) vinculado(s) ao setor: </strong>
                         <ul>
                        {foreach $setor->getUsuarios() as $usuario}
                            <li>{$usuario}</li>
                        {/foreach}
                         </ul>
                    </div>
                {/if}
            </div>
        </div>
    {else}
        <div class="form-group">
            <label class="col-form-label">Subt-Setor de:</label>
            <select name="setor_pai_id" class="select2Tree">
                <option value=""></option>
                {include file="../../Setor/Templates/select.tpl"}
            </select>
        </div>
        <div class="form-group row">
            <div class="col">
                <label class="col-form-label required">Setor:</label>
                <input type="text" autofocus="true" class="form-control form-control-sm maiscula"
                       value="{$setor->getNome()}" name="nome" required="true">
            </div>
            <div class="col-md-3">
                <label class="col-form-label">Sigla:</label>
                <input type="text" class="form-control form-control-sm maiscula" required value="{$setor->getSigla()}"
                       name="sigla">
            </div>
            <div class="col-md-3 d-none">
                <label class="col-form-label">Ativo?</label><br/>
                <div class="form-check form-check-inline">
                    <label class="custom-control custom-radio">
                        <input id="setorAtivo" name="isAtivo" value="1" type="radio" class="custom-control-input"
                               {if $setor->getIsAtivo() eq true}checked{/if}>
                        <label class="custom-control-label" for="setorAtivo">Sim</label>
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <label class="custom-control custom-radio">
                        <input id="setorInativo" name="isAtivo" value="0" type="radio" class="custom-control-input"
                               {if $setor->getIsAtivo() eq false}checked{/if}>
                        <label class="custom-control-label" for="setorInativo">Não</label>
                    </label>
                </div>
            </div>
        </div>
                     
        <div  class="form-group row">
            <div class="col-3 divOrgao{if   $setor->getSetorPai()->getId()} d-none {/if}" >
                <label class="col-form-label not-required  " >Órgão:</label>
                <input type="text" class="form-control form-control-sm codigo-orgao"
                       value="{$setor->getOrgao()}" name="orgao" >
            </div>
            <div class="col-5 divUnidade {if !$setor->getSetorPai() || !$setor->getSetorPai()->getId()} d-none {/if}">
                <label class="col-form-label not-required " >Unidade:</label>
                <input type="text" class="form-control form-control-sm codigo-unidade"
                       name="unidade" value="{$setor->getUnidade()}" >
            </div>
        </div>
        <div class="form-group row" >
            <div class="col">
                <label class="col-form-label">Externo?
                    <i data-toggle="popover" data-html="true" data-content="Quando essa opção estiver marcada o assunto ficará disponível para interação com os interessados. " class="fa fa-question-circle text-info tooltip-icon"></i>
                </label><br/>
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-radio">
                        <input id="radioExterno1" type="radio" name="isExterno" value="1" class="custom-control-input" {if $setor->getIsExterno() eq true}checked{/if}>
                        <label class="custom-control-label" for="radioExterno1">Sim</label>
                    </div>
                </div>
                <div class="form-check form-check-inline">
                    <div class="custom-control custom-radio">
                        <input id="radioExterno2" type="radio" name="isExterno" value="0" class="custom-control-input" {if $setor->getIsExterno() eq false}checked{/if}>
                        <label class="custom-control-label" for="radioExterno2">Não</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="arquivar" value="1" class="custom-control-input"
                       id="customCheckArquivar" {if $setor->getArquivar() eq true}checked{/if}>
                <label class="custom-control-label" for="customCheckArquivar">Marque para arquivar quando enviar para esse
                    setor.</label>
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="disponivel_tramite" value="1" class="custom-control-input"
                       id="customCheckDisponivelTramite" {if $setor->getDisponivelTramite() eq true}checked{/if}>
                <label class="custom-control-label" for="customCheckDisponivelTramite">Marque caso o setor esteja disponível para trâmite.</label>
            </div>
        </div>
        
    {/if}
   
    <hr>
    {*<div class="form-group row">
        <div class="col">
            <label class="col-form-label">Sub Setor de: <strong>{$setor->getSetorPai()}</strong></label>
            <input id="search_setor" placeholder="Digite o setor para buscar" type="text" class="form-control form-control-sm"/>
            <div id="jstree"></div>
        </div>
    </div>*}
    <div class="form-group">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i class="fa fa-save"></i>
            Salvar
        </button>
        
        {if !isset($modal) || $modal eq false}
            <a href="{$app_url}setor" class="btn btn-light border"><i class="fa fa-times"></i> Cancelar</a>
        {else}
            <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i class="fa fa-times"></i> Cancelar</a>
        {/if}
    </div>
</form>


<script defer type="text/javascript" src="{$app_url}assets/js/view/setor/formulario.js?v={$file_version}"></script>


