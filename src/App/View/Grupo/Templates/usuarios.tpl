<table id="tabelaUsuarioGrupo" class="table table-bordered table-sm text-center table-hover">
    <thead class="thead-light">
        <tr>
            <th>Usuário</th>
            <th>E-mail</th>
            <th>Ativo?</th>
            <th>Último login</th>
        </tr>
    </thead>
    <tbody>
        {foreach $grupo->getUsuarios() as $usuario}
            <tr class="visualizar-entidade" entidade="Usuario" entidade_id="{$usuario->getId()}" modal_size="">
                <td class="text-left">{$usuario->getPessoa()->getNome()}</td>
                <td class="text-left">
                    {$usuario->getPessoa()->getEmail()}
                </td>
                <td>
                    {if $usuario->getAtivo() eq 1}
                        <label class="badge badge-success">SIM</label>
                    {else}
                        <label class="badge badge-danger">NÃO</label>
                    {/if}
                </td>
                <td>
                    {if $usuario->getUltimoLogin() neq ""}
                        {$usuario->getUltimoLogin()->format('d/m/Y  - H:i:s')}
                    {else}
                        Não registrado
                    {/if}
                </td>
            </tr>
        {foreachelse}
            <tr>
                <td colspan="3">*Nenhum usuário vinculado.</td>
            </tr>
        {/foreach}
</table>

