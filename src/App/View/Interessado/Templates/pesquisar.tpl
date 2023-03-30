<form id="formPesquisaInteressado">
    <input type="hidden" name="interessado_id" id="interessado_id">
    <input type="hidden" name="nome_interessado" id="nome_interessado">
    <p class="text-info lead">Selecione um interessado da lista abaixo:</p>
    <table id="tabelaPesquisaInteressados" class="table table-hover table-sm"
           url="{$app_url}src/App/Ajax/Interessado/listar_server_side.php">
        <thead class="bg-light">
        <tr>
            <th>Cód.#</th>
            <th>Nome</th>
            <th>C.P.F./C.N.P.J</th>
            <th></th>
            <th>CPF</th>
            <th>CNPJ</th>
        </tr>
        <tr>
            <td class="w-25">
                <input type="number" placeholder="Pesquisar por código" name="codigo_filter"
                       class="codigo_filter form-control-sm form-control"/>
            </td>
            <td>
                <input type="text" name="nome_filter" placeholder="Pesquisar por nome"
                       class="nome_filter form-control-sm form-control"/>
            </td>
            <td class="w-25">
                <input type="text" name="cpf_cnpj_filter" placeholder="Pesquisar por CPF/CNPJ"
                       class="form-control-sm form-control cpf_cnpj_filter"/>
            </td>
        </tr>
        </thead>
        <tbody style="cursor:pointer;"></tbody>
    </table>
    <hr/>
    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> OK</button>
    <button type="button" class="btn btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                class="fa fa-times"></i> Cancelar
    </button>
</form>