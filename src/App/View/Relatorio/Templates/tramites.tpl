<form target="_blank" method="POST" action="{$app_url}processo/tramites">
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
                <input type="text" value="{$hoje}" id="periodoFim" name="periodoFim" class="form-control" required>
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
        <div class="col-lg">
            <label class="col-form-label">Usuário:</label>
            <select id="" class="form-control select_usuario" name="usuario_id">
                <option value=""></option>
            </select>
        </div>
    </div>
    <hr />
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Gerar</button>
        <button type="reset" class="btn btn-light border"><i class="fa fa-refresh"></i> Limpar</button>
    </div>
</form>