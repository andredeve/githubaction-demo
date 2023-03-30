<div class="card">   
    <div class="card-header">
        <h6 class="text-info">
        <i class="fa fa-user"></i> 
            {if !empty($usuario->getPessoa())}
                {$usuario->getPessoa()->getNome()}
            {else}
                Não Encontrado
            {/if}  
        </h6>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Nome:</dt>
            <dd class="col-sm-9">
                {if !empty($usuario->getPessoa())}
                    {$usuario->getPessoa()->getNome()}
                {else}
                    Não Encontrado
                {/if} 
            </dd>
            <dt class="col-sm-3">Cargo:</dt>
            <dd class="col-sm-9">{$usuario->getCargo()}</dd>
            <dt class="col-sm-3">E-mail:</dt>
            <dd class="col-sm-9"><a target="_blank" href="mailto:{if !empty($usuario->getPessoa())}{$usuario->getPessoa()->getEmail()}{else}Não Encontrado{/if}">{if !empty($usuario->getPessoa())}{$usuario->getPessoa()->getEmail()}{else}Não Encontrado{/if}</a></dd>
            <dt class="col-sm-3">Data Cadastro:</dt>
            <dd class="col-sm-9">{$usuario->getDataCadastro(true)}</dd>
            <dt class="col-sm-3">Alterado em:</dt>
            <dd class="col-sm-9">{$usuario->getUltimaAlteracao(true)}</dd>
            <dt class="col-sm-3">Último Login:</dt>
            <dd class="col-sm-9">{$usuario->getUltimoLogin(true)}</dd>
            <dt class="col-sm-3">Perfil:</dt>
            <dd class="col-sm-9">{$usuario->getTipo(true)}</dd>
            <dt class="col-sm-3">Grupo:</dt>
            <dd class="col-sm-9">{$usuario->getGrupo()}</dd>
            <dt class="col-sm-3">Ativo?</dt>
            <dd class="col-sm-9">{$usuario->getAtivo(true)}</dd>
            <dt class="col-sm-3">Acesso aos setores:</dt>
            <dd class="col-sm-9">
                <ul class="list-unstyled">
                    {foreach $usuario->getSetores() as $setor}
                        <li><i class="fa fa-check-square-o"></i> {$setor}</li>
                    {/foreach}
                </ul>
            </dd>
        </dl>
    </div>
</div>
<div class="mt-3">
    <a class="btn btn-info btn-sm btn-loading" title="Editar" href="{$app_url}usuario/editar/id/{$usuario->getId()}"><i class="fa fa-edit"></i> Editar</a>
    <a class="btn btn-danger btn-sm btn-excluir" title="Remover" href="{$app_url}usuario/excluir/id/{$usuario->getId()}"><i class="fa fa-remove"></i> Excluir</a>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Fechar</button>
</div>
