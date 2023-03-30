
<table class="table table-bordered table-sm datatable">
    <thead>
        <tr>
            <th></th>
        </tr>
    </thead>
</table>

<form id="mesclarAnexosForm" action="{$app_url}anexo/mesclar">
    <input type="hidden" id="anexos_mesclar" name="anexos_mesclar"/>
    <input type="hidden" id="anexo_manter_id" name="anexo_manter_id"/>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Tipo:</label>
            <a id="cadastrar:TipoAnexo" href_select="select_tipo_documento" href="#"
               title="Cadastrar novo Tipo de Documento" class="btn btn-xs btn-success pull-right btn-cadastrar-modal"><i
                        class="fa fa-plus"></i></a>
            <select id="select_tipo_documento" name="tipo_documento_id" class="form-control select2" required="">
                <option value="">Selecione</option>
                {foreach $tipos_documento as $tipo}
                    <option value="{$tipo->getId()}">{$tipo->getDescricao()}</option>
                {/foreach}
            </select>
        </div>
        <div class="col">
            <label class="col-form-label">Classificação:</label>
            <a id="cadastrar:Classificacao" href_select="select_classificacao_documento" href="#"
               title="Cadastrar nova Classificação de Documento"
               class="btn btn-xs btn-success pull-right btn-cadastrar-modal"><i class="fa fa-plus"></i></a>
            <select id="select_classificacao_documento" name="classificacao_documento_id" class="form-control select2">
                <option value="">Selecione</option>
                {foreach $classificacoes as $classificacao}
                    <option value="{$classificacao->getId()}">{$classificacao}</option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label">Descrição:</label>
        <input type="text" name="descricao_doc" id="descricao_doc" value=""
               class="form-control form-control-sm"/>
    </div>
    <div class="form-group row">
        <div class="col">
            <label class="col-form-label required">Data:</label>
            <input id="data_doc" autocomplete="off" value="" type="text" name="data_doc"
                   class="form-control form-control-sm datepicker" required=""/>
        </div>
        <div class="col">
            <label class="col-form-label">Número:</label>
            <input id="numero_doc" type="text" value="" name="numero_doc"
                   class="form-control form-control-sm" required=""/>
        </div>
        <div class="col">
            <label class="col-form-label">Valor:</label>
            <input id="valor_doc" type="text" value="" name="valor_doc"
                   class="form-control form-control-sm autonumeric"/>
        </div>
        <div class="col">
            <label class="col-form-label">Páginas:</label>
            <input id="paginas_doc" type="number" min="1" value="" name="paginas_doc"
                   class="form-control form-control-sm"/>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="text-center">
                <span class="lead"><i class="fa fa-paperclip"></i> Documentos anexos</span>
            </div>
            <ul id="sortable1" class="list-group connectedSortable w-100">
                {foreach $processo->getAnexos() as $anexo}
                    {if $anexo->getExtensao() eq 'pdf'}
                        <li title="Clique e arraste" paginas="{$anexo->getQtdePaginas()}" id="{$anexo->getId()}"
                            class="list-group-item ui-state-default">
                                <div class="btn-group w-100" role="group" >
                                    <button type="button" class="btn col-10 btn-sm btn-danger btn-converter-pdf" anexo="{$anexo->getId()}"  title="PDF com formato inrregular, clique aqui para iniciar a conversão">Converter PDF</button>
                                    <button type="button" class="btn col-2 btn-sm btn-warning btn-listar-anexos-converter" title="Ver fila de conversão"> <i class="fa fa-list"></i></button>
                                </div>
                            <br>
                            <div class="alert alert-danger">
                                <small > 
                                    Este anexo está com formato incorreto, 
                                    clique em converter para corrigir.
                                </small>
                            </div>
                            <br>
                            <small>
                                <i class="fa fa-paperclip"></i> {$anexo->getTipo()} 
                                <br/>Nº {$anexo->getNumero()} - {$anexo->getData(true)}
                                <br/>
                                <small>{$anexo->getDescricao()}</small>
                                <br/>
                                <small>({$anexo->getQtdePaginas()} páginas - {$anexo->getTamanho()})</small>
                            </small>
                            
                        </li>
                    {/if}
                {/foreach}
            </ul>
        </div>
        <div class="col">
            <div class="text-center">
                <span class="lead"><i class="fa fa-sitemap"></i> Mesclar</span>
            </div>
            <ul id="sortable2" class="list-group connectedSortable w-100">

            </ul>
        </div>
    </div>
    <div class="alert alert-warning mt-2">
        <strong><i class="fa fa-info-circle"></i></strong> O arquivo mesclado gerado será composto pelos anexos na ordem
        adicionada na coluna
        "Mesclar" a direita.
    </div>
    <hr/>
    <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Mesclar</button>
    <button type="button" onclick="$(this).closest('.modal').modal('hide');" class="btn btn-light border"><i
                class="fa fa-times"></i> Cancelar
    </button>
</form>

