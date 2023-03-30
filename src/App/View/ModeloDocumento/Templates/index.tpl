<div>
    <a title="Cadastre um novo Modelo de Documento" class="btn btn-primary btn-loading"
   href="{$app_url}modeloDocumento/cadastrar"><i class="fa fa-plus"></i> Novo</a> 
   <span class="pull-right"><i class="mr-1 fa fa-info-circle" styl></i>Gere documentos automaticamente a partir de modelos pré-cadastrados.</span>
</div>
<hr/>
<div class="card-columns">
    {foreach $modelos as $modelo}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{$modelo->getNome()}</h5>
                <p class="card-text">
                    Criado em: <span class="text-muted">{$modelo->getDataCadastro(true)}</span><br/>
                    Alterado em: <span class="text-muted">{$modelo->getUltimaAlteracao(true)}</span>
                </p>
                <a title="Editar" class="btn btn-sm btn-light border btn-loading"
                   href="{$app_url}modeloDocumento/editar/id/{$modelo->getId()}"><i class="fa fa-edit"></i>
                </a>
                <a title="Baixar template" class="btn btn-sm btn-light border" href="{$modelo->getArquivo(false,true)}"><i
                            class="fa fa-download"></i></a>
                <a title="Excluir" class="btn btn-sm btn-light border btn-excluir"
                   href="{$app_url}modeloDocumento/excluir/id/{$modelo->getId()}"><i
                            class="fa fa-trash"></i>
                </a>
            </div>
        </div>
    {/foreach}
</div>