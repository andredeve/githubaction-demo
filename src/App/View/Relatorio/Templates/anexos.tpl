<div class="card">
    <div class="card-body">
        <form id="formAnexosPeriodo" class="form-horizontal" method="POST">
            <div class="form-group row">
                <div class="col">
                    <label>Tipo de documento:</label>
                    <select id="select_tipo_documento" name="tipo_documento_id" class="select2 form-control form-filter">
                        <option value="">Todos</option>
                        {foreach $tipos_documento as $tipo}
                            <option value="{$tipo->getId()}">{$tipo->getDescricao()}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col">
                    <label>Período (de):</label>
                    <div class="input-group">
                        <input id="data_periodo_ini" data_fim_id="data_periodo_fim"  type="text" name="periodo_ini" 
                              value="{$data_ini}" class="form-control form-control-sm date-range form-filter"/>
                        <div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    </div>
                </div>
                <div class="col">
                    <label>Período(até):</label>
                    <div class="input-group">
                        <input id="data_periodo_fim" type="text" name="periodo_fim" 
                               value="{$data_fim}" class="form-control form-control-sm form-filter"/>
                        <div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                    </div>
                </div>
                <div class="col">
                    <label>Usuário:</label>
                    <select id="select_usuario" name="usuario_id" class="select2 form-control form-filter">
                        <option value="">Todos</option>
                        {foreach $usuarios as $usuario}
                            <option value="{$usuario->getId()}">{$usuario->getPessoa()->getNome()}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
<br/>
<div class="row">
    <div class="col-md-4">
        {*<div id="pieFormaAnexo"></div>*}
        <div id="piePorTipoAnexo"></div>
    </div>
    
</div>
        <div class="row">
            <div class="col">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active"  data-toggle="tab" href="#detalhadoTab" role="tab" aria-controls="detalhadoTab">Detalhado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#quantitativoTab" role="tab" aria-controls="quantitativoTab">Quantitativo</a>
                    </li>                    
                </ul>
            </div>   
            
                <div id="divListaAnexos">
                    {*<div class="tab-pane fade show active" id="detalhadoTab" role="tabpanel">*}
                        {include file="../../Anexo/Templates/listar_relatorio.tpl"}
                    {*</div>*}
                </div>
            
            
        </div>
        
    </div>
        </div>
<script defer type="text/javascript" src="{$app_url}min/g=datatableButtonsJs"></script>

