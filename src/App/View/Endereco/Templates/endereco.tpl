{$endereco=$objeto->getEndereco()}
<input type="hidden" name="endereco_id" value="{$endereco->getId()}"/>
<div class="form-group row">
    <div class="col-md-4 col-sm-12">
        <label class="col-form-label">CEP:</label>
        <div class="input-group">
            <input type="text" name="cep" id="cep" value="{$endereco->getCep()}"
                   class="form-control form-control-sm cep"/>
            <div class="input-group-append">
                <div class="input-group-text"><i class="fa fa-search"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <label class="col-form-label">Rua:</label>
        <input id="rua" type="text" name="rua" value="{$endereco->getRua()}" class="form-control form-control-sm"/>
    </div>
    <div class="col-md-2 col-sm-12">
        <label class="col-form-label">Nº.:</label>
        <input id="numero" type="text" maxlength="4" name="numero" value="{$endereco->getNumero()}"
               class="form-control form-control-sm"/>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-12">
        <label class="col-form-label">Bairro:</label>
        <input id="bairro" type="text" name="bairro" value="{$endereco->getBairro()}"
               class="form-control form-control-sm"/>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-3 col-sm-12">
        <label class="col-form-label">UF:</label>
        <select id="estado" name="uf" class="form-control form-control-sm estado">
            {$estado=$endereco->getEstado()}
            <option value="">Selecione</option>
            {foreach $estados as $es}
                <option value="{$es->getUf()}"
                        {if $estado neq null and $estado->getId() eq $es->getId()}selected{/if} >{$es->getUf()}</option>
            {/foreach}
        </select>
    </div>
    <div class="col-md-9 col-sm-12">
        <label class="col-form-label">Cidade:</label>
        <select id="cidade" name="cidade" class="form-control form-control-sm cidade">
            <option value="">Selecione</option>
            {if $endereco->getEstado() neq null}
                {foreach $estado->getCidades() as $ci}
                    <option value="{$ci->getId()}"
                            {if $endereco->getCidade() neq null and $ci->getId() eq $endereco->getCidade()->getId()}selected{/if}>{$ci}</option>
                {/foreach}
            {/if}
        </select>
    </div>
</div>
<div class="form-group row">
    <div class="col-sm-12">
        <label class="col-form-label">Complemento:</label>
        <textarea name="complemento" rows="2"
                  class="form-control form-control-sm">{$endereco->getComplemento()}</textarea>
    </div>
</div>
{if $objeto->getId() neq null}
    <a target="_blank" href="http://www.google.com.br/maps/place/{$endereco}"><i
                class="fa fa-map-marker col-md-offset-2"></i> Ver localização no mapa</a>
{/if}
