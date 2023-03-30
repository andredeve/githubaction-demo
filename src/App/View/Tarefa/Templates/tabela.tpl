<ul class="list-group lista-tarefas">
    {if count($tarefas) gt 0}
        {foreach $tarefas as $tarefa}
            <li title="Clique, segure, arraste e depois solte para alterar a ordem da tarefa." id="item-{$tarefa->getId()}" class="list-group-item list-group-item-action linha_tarefa_{$setor_fase->getId()} {if $tarefa->getIsAtiva() eq false}disabled{/if}">
                <div class="row">
                    <div class="col-md-1 text-center vertical-middle">
                        {if $tarefa->getIsAtiva() eq 1}
                            <i title="Tarefa ativa" class="fa fa-check text-success fa-lg"></i>
                        {else}
                            <i title="Tarefa inativa" class="fa fa-ban text-secondary fa-lg"></i>
                        {/if}
                    </div>
                    <div class="text-justify col-md-9">
                        <span class="lead">{$tarefa@iteration}</span>.&nbsp;&nbsp;{$tarefa->getDescricao()}
                    </div>
                    <div class="col-md-2 text-right">
                        <button entidade="Tarefa" entidade_id="{$tarefa->getId()}" objeto_ref_id="{$setor_fase->getId()}" type="button"  class="btn btn-info btn-xs btn-editar-entidade" title="Editar"><i class="fa fa-edit"></i></button>
                        <a entidade="Tarefa" objeto_ref_id="{$setor_fase->getId()}" class="btn btn-danger btn-xs btn-excluir-entidade" title="Excluir" href="{$app_url}tarefa/excluir/id/{$tarefa->getId()}"><i class="fa fa-trash-o"></i></a>
                    </div>
                </div>
            </li>
        {/foreach}
    {else}
        <li class="list-group-item no-sortable">*Nenhuma tarefa encontrada.</li>
        {/if}
</ul>

