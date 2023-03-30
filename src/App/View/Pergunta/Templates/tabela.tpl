<ul class="list-group lista-perguntas">
    {if count($perguntas) gt 0}
        {foreach $perguntas as $pergunta}
            <li title="Clique, segure, arraste e depois solte para alterar a ordem da pergunta." id="item-{$pergunta->getId()}" class="list-group-item list-group-item-action linha_pergunta_{$setor_fase->getId()} {if $pergunta->getIsAtiva() eq false}disabled{/if}">
                <div class="row">
                    <div class="col-md-1 text-center vertical-middle">
                        {if $pergunta->getIsAtiva() eq 1}
                            <i title="Pergunta ativa" class="fa fa-check text-success fa-lg"></i>
                        {else}
                            <i title="Pergunta inativa" class="fa fa-ban text-secondary fa-lg"></i>
                        {/if}
                    </div>
                    <div class="text-justify col-md-9">
                        <span class="lead">{$pergunta@iteration}</span>.&nbsp;&nbsp;{$pergunta->getDescricao()}
                    </div>
                    <div class="col-md-2 text-right">
                        <button entidade="Pergunta" entidade_id="{$pergunta->getId()}" objeto_ref_id="{$setor_fase->getId()}" type="button"  class="btn btn-info btn-xs btn-editar-entidade" title="Editar"><i class="fa fa-edit"></i></button>
                        <a entidade="Pergunta" objeto_ref_id="{$setor_fase->getId()}" class="btn btn-danger btn-xs btn-excluir-entidade" title="Excluir" href="{$app_url}pergunta/excluir/id/{$pergunta->getId()}"><i class="fa fa-trash-o"></i></a>
                    </div>
                </div>
            </li>
        {/foreach}
    {else}
        <li class="list-group-item no-sortable">*Nenhuma pergunta encontrada.</li>
        {/if}
</ul>

