
<div class="row">
    <div class="col-sm-2 float-right">
        <form class="form form-group ">
            <label for="usuario_status_filter">Listar Inativos?</label>
            <select class="form-control-sm select2 usuario_status_filter" name="usuario_status_filter" id="usuario_status_filter" onchange="usuarioListaInativo(value)">
                <option value="1">Sim</option>
                <option value="0" selected>Não</option>
                <option value="-1">Todos</option>
            </select>
        </form>
    </div>
</div>
<table id="tabelaListagemUsuarios" class="table table-bordered table-hover datatable table-sm">
    <thead class="thead-light">
        <tr>
            <th>Cód.#</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Grupo</th>
            <th class="text-center">Ativo?</th>
            <th class="text-center">Último Login</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach $usuarios as $usuario}
            <tr class="visualizar-entidade" entidade="Usuario" entidade_id="{$usuario->getId()}" modal_size="">
                <td>{$usuario->getId()}</td>
                <td class="text-left">
                    {if isset($usuario->getPessoa())}
                        {$usuario->getPessoa()->getNome()}
                    {else}          
                        Não encontrado  
                    {/if}  
                </td>
                <td class="text-left">
                    {if isset($usuario->getPessoa())}
                        {$usuario->getPessoa()->getEmail()}
                    {else}          
                        Não encontrado  
                    {/if}  
                </td>
                <td>{$usuario->getGrupo()}</td>
                <td class="text-center">
                    {if $usuario->getAtivo() eq 1}
                        <label class="badge badge-success">SIM</label>
                    {else}
                        <label class="badge badge-danger">NÃO</label>
                    {/if}
                </td>
                <td class="text-center">
                    {if $usuario->getUltimoLogin() neq ""}
                        {$usuario->getUltimoLogin()->format('d/m/Y  - H:i:s')}
                    {else}
                        Não registrado
                    {/if}
                </td>
                <td>
                    <a class="btn btn-info btn-xs btn-loading btn-editar-no-propagate" title="Editar" href="{$app_url}usuarioContribuinte/editar/id/{$usuario->getId()}"><i class="fa fa-edit"></i></a>
                   <!--  <a class="btn btn-danger btn-xs btn-excluir" title="Excluir" href="{$app_url}usuario/excluir/id/{$usuario->getId()}"><i class="fa fa-user-times"></i></a> !-->
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>