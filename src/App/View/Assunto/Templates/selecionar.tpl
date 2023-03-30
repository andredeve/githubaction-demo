<form id="formPesquisaAssunto">
    <input type="hidden" name="entidade_id" id="entidade_id">
    <input type="hidden" name="entidade_descricao" id="entidade_descricao">
    <input type="hidden" name="listar_externos" id="listar_externos" value="{$usuario_logado->getTipo() == App\Enum\TipoUsuario::INTERESSADO}">
    <p class="text-info lead">Selecione um assunto da lista abaixo:</p>
    <table id="tabelaPesquisaSelecionarAssunto" class="table table-hover table-sm"
           url="{$app_url}src/App/Ajax/Assunto/listar_server_side.php?server=1" cols_select="0,1" cols_hidden="2" sorter="1">
        <thead class="bg-light">
        <tr>
            <th server_side="true" col_name="id">Cód.#</th>
            <th server_side="true" col_name="nome">Assunto</th>
            <th>Prazo</th>
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