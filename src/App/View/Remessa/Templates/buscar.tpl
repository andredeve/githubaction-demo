<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link" href="{$app_url}remessa"><i class="fa fa-share"></i> Histórico de
                    Remessas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{$app_url}remessa/buscar"><i class="fa fa-file-o"></i> Gerar
                    Nova</a>
            </li>
        </ul>
    </div>
    <div class="card-body w-75">
        <form id="buscarRemessaForm">
            <div class="form-group row">
                <div class="col">
                    <label class="col-form-label">Período de:</label>
                    <div class="input-group">
                        <input type="text" data_fim_id="periodoFim" id="periodoIni" value="{$hoje}" name="periodoIni"
                               class="form-control form-control-sm date-range" required="true">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <label class="col-form-label">Período até:</label>
                    <div class="input-group">
                        <input type="text" id="periodoFim" id="periodoFim" value="{$hoje}" name="periodoFim"
                               class="form-control form-control-sm" required="true>
                        <div class=" input-group-append">
                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-header text-center p-2">Local de Origem</div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">Setor de Origem:</label>
                            <select name="setor_origem_id" class="select2Tree">
                                         <option value=""></option>
                            {include file="../../Setor/Templates/select.tpl"}
                            </select>
                        </div>
                        <div class=" col">
                            <label class="col-form-label required">Responsável de Origem:</label>
                            <select name="responsavel_origem_id" class="select2">
                                         <option value=""></option>
                            {foreach $responsaveis as $responsavel}
                                <option value="{$responsavel->getId()}">{$responsavel}</option>
                            {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header text-center p-2">Local de Destino</div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">Setor de Destino:</label>
                            <select name="setor_destino_id" class="select2Tree">
                                          <option value=""></option>
                            {include file="../../Setor/Templates/select.tpl"}
                            </select>
                        </div>
                        <div class="col">
                            <label class="col-form-label">Responsável de Destino:</label>
                            <select name="responsavel_destino_id" class="select2">
                                        <option value="">Selecione</option>
                            {foreach $responsaveis as $responsavel}
                                <option value="{$responsavel->getId()}">{$responsavel}</option>
                            {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            <div>
                <button class="btn btn-primary ladda-button" type="submit"><i class="fa fa-search"></i> Buscar</button>
                <a class="btn btn-light btn-loading border" href="{$app_url}remessa"><i class="fa fa-times"></i>
                    Cancelar</a>
            </div>
        </form>
    </div>
</div>