<form id="formPesquisaSetor">
    <input type="hidden" name="entidade_id" id="entidade_id">
    <input type="hidden" name="entidade_descricao" id="entidade_descricao">
    <p class="text-info lead">Selecione um setor da lista abaixo:</p>
    <table id="tabelaPesquisaSelecionarSetor" class="table table-hover table-sm" cols_descricao="3"
           url="{$app_url}src/App/Ajax/Setor/listar_server_side.php?server=1"  sorter="1">
        <thead class="bg-light">
        <tr>
            <th server_side="true" col_name="id">Cód.#</th>
            <th server_side="true" col_name="orgao">Órgão</th>
            <th server_side="true" col_name="unidade">Unidade</th>
            <th server_side="true" class="col" col_name="nome">Setor</th>
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