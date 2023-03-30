<ul class="list-group lista-campos">
    {if count($campos) gt 0}
        {foreach $campos as $campo}
            <li title="Clique, segure, arraste e depois solte para alterar a ordem da campo." id="item-{$campo->getId()}"
                class="list-group-item list-group-item-action linha_campo_{$setor_fase->getId()}">
                <div class="row">
                    <div class="text-justify col-md-10 {if $campo->isAtivo() eq false}strikethrough{/if}">
                        <span class="lead">{$campo@iteration} .</span>&nbsp;&nbsp;{$campo->getNome()}<br/>
                        <span class="badge badge-secondary">{App\Enum\TipoCampo::getDescricao($campo->getTipo())}</span>
                        {if in_array($campo->getTipo(),array(App\Enum\TipoCampo::ARQUIVO, App\Enum\TipoCampo::ARQUIVO_MULTIPLO))}
                            <span class="badge badge-secondary">{if !is_null($campo->getTipoTemplate())}{$campo->getTipoTemplate()->getDescricao()}{/if}</span>
                        {/if}
                        {if $campo->getCirculacaoInterna()}
                            <span class="badge badge-secondary">Interno</span>
                        {/if}
                        {if $campo->getIsObrigatorio() eq true}
                            <span class="text-danger">* Preenchimento obrigatório.</span>
                        {/if}
                        <br/>
                        <small class="text-muted">{$campo->getDescricao()}</small>
                    </div>
                    <div class="col-md-2 text-right">
                        <button entidade="Campo" entidade_id="{$campo->getId()}" objeto_ref_id="{$setor_fase->getId()}"
                                type="button"  class="btn btn-info btn-xs btn-editar-entidade"
                                title="Editar" {if $campo->isAtivo() eq false}disabled=true{/if}>
                            <i class="fa fa-edit"></i>
                        </button>
                        <a entidade="Campo" objeto_ref_id="{$setor_fase->getId()}"
                           class="btn btn-danger btn-xs btn-excluir-entidade {if $campo->isAtivo() eq false}disabled{/if}"
                           title="Excluir" href="{$app_url}Campo/excluir/id/{$campo->getId()}">
                            <i class="fa fa-trash-o"></i>
                        </a>
                        {if $campo->isAtivo() eq false}
                            <a entidade="Campo" objeto_ref_id="{$setor_fase->getId()}"
                               class="btn btn-success btn-xs btn-reativar-entidade"
                               title="Reativar" href="{$app_url}Campo/reativar/id/{$campo->getId()}">
                                <i class="fa fa-undo"></i>
                            </a>
                        {/if}
                    </div>
                </div>
            </li>
        {/foreach}
    {else}
        <li class="list-group-item no-sortable">*Nenhum campo adicionado.</li>
    {/if}
</ul>

