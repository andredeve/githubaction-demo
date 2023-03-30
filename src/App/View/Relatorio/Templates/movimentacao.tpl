<form target="_blank" method="POST" action="{$app_url}processo/movimentacao">
    <div class="form-group row">
        <div class="col-2">
            <label class="col-form-label">Período de:</label>
            <div class="input-group">
                <input data_fim_id="periodoFim" value="{$hoje}" type="text" id="periodoIni" name="periodoIni"
                       class="form-control date-range" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="col-2">
            <label class="col-form-label">Período até:</label>
            <div class="input-group">
                <input type="text" value="{$hoje}" id="periodoFim" name="periodoFim"
                       class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="col">
            <label class="col-form-label">Setor:</label>
            <select name="setor_id" class="select2Tree">
                <option value=""></option>
                {include file="../../Setor/Templates/select.tpl"}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-form-label">Interesssado:</label>
        <select id="" class="form-control select_interessado" name="interessado_id">
            <option value=""></option>
        </select>
    </div>
    <div class="form-group">
        <label class="col-form-label">Assunto:</label>
        <select class="form-control select_assunto" name="assunto_id">
            <option value=""></option>
        </select>
    </div>
    <hr/>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Gerar</button>
        <button type="reset" class="btn btn-light border"><i class="fa fa-refresh"></i> Limpar</button>
    </div>
</form>