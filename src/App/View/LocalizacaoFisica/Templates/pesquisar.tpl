<div class="modal fade" id="pesquisarLocalizacaoFisica" role="dialog"
     aria-labelledby="pesquisarLocalizacaoFisica"
     aria-hidden="true">
    <form id="pesquisaLocalizacaoFisicaForm">
        <input type="hidden" name="pesquisar" value="ok"/>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pesquisar Endereçamento (Localização Física)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">Exercício:</label>
                            <select name="exercicio" class="form-control form-control-sm">
                                <option value="">Todos</option>
                                {foreach $anos as $ano}
                                    <option value="{$ano}">{$ano}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col">
                            <label class="col-form-label">Número Documento (de):</label>
                            <input type="number" class="form-control form-control-sm" name="numero_documento_ini"/>
                        </div>
                        <div class="col">
                            <label class="col-form-label">Número Documento (até):</label>
                            <input type="number" class="form-control form-control-sm" name="numero_documento_fim"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">Data Documento (de:)</label>
                            <input id="data_documento_ini" data_fim_id="data_documento_fim" type="text"
                                   name="data_documento_ini"
                                   class="form-control form-control-sm date-range data"/>
                        </div>
                        <div class="col">
                            <label class="col-form-label">Data Documento (até):</label>
                            <input id="data_documento_fim" type="text" name="data_documento_fim"
                                   class="form-control form-control-sm data"/>
                        </div>
                        <div class="col">
                            <label class="col-form-label">Data Cadastro (de:)</label>
                            <input id="data_cadastro_ini" data_fim_id="data_cadastro_fim" type="text"
                                   name="data_cadastro_ini"
                                   class="form-control form-control-sm date-range data"/>
                        </div>
                        <div class="col">
                            <label class="col-form-label">Data Cadastro (até):</label>
                            <input id="data_cadastro_fim" type="text" name="data_cadastro_fim"
                                   class="form-control form-control-sm data"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">Local:</label>
                            <select data-placeholder="Todos" name="local" class="form-control select2 form-control-sm">
                                <option value=""></option>
                                {foreach $locais as $local}
                                    <option value="{$local}">{$local}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="col-form-label">Ref.:</label>
                            <input type="text" name="refLocal" class="form-control form-control-sm"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">Tipo de Local:</label>
                            <select data-placeholder="Todos" name="tipoLocal"
                                    class="form-control select2 form-control-sm">
                                <option value=""></option>
                                {foreach $tipos_local as $local}
                                    <option value="{$local}">{$local}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="col-form-label">Ref.:</label>
                            <input type="text" name="refTipoLocal" class="form-control form-control-sm"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">SubTipo de Local:</label>
                            <select data-placeholder="Todos" name="subTipoLocal"
                                    class="form-control select2 form-control-sm">
                                <option value=""></option>
                                {foreach $subtipos_local as $local}
                                    <option value="{$local}">{$local}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-3">
                            <label class="col-form-label">Ref.:</label>
                            <input type="text" name="refSubTipoLocal" class="form-control form-control-sm"/>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group">
                        <label>Buscar por texto:</label>
                        <select name="tipo_texto" class="">
                            <option value="0">Contém</option>
                            <option value="1">Inicia</option>
                            <option value="2">igual</option>
                        </select>
                        <label>Referência: </label>
                        <select name="ref_texto" class="">
                            <option value="0">Tudo</option>
                            <option value="1">Ementa</option>
                            <option value="2">Observação</option>
                        </select>
                        <textarea name="texto" placeholder="Digite aqui uma palavra ou texto para pesquisar"
                                  class="form-control form-control-sm"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary ladda-button">Pesquisar</button>
                </div>
            </div>
        </div>
    </form>
</div>
