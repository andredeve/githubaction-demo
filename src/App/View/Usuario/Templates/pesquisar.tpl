<form id="formPesquisaUsuario">
    <input type="hidden" name="usuario_id" id="usuario_id">
    <input type="hidden" name="nome_usuario" id="nome_usuario">
    <p class="text-info lead">Selecione um usuário da lista abaixo:</p>
    <table id="tabelaPesquisaUsuarios" class="table table-hover table-sm"
           url="{$app_url}src/App/Ajax/Usuario/listar_server_side.php">
        <thead class="bg-light">
            <tr>
                <th>Cód.#</th>
                <th>Nome</th>
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