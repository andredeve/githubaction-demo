{function option_setor level=1}
    {foreach $setores as $setor}
        {$filhos=$setor->getSetoresFilhos()}
        {$qtde_filhos=count($filhos)}
        <option qtde_filhos="{$qtde_filhos}"
                {if $setor_sel neq null and  in_array($setor->getId(),$setor_sel)}selected{/if}
                value="{$setor->getId()}" {if $setor_pai_id neq null}data-pup="{$setor_pai_id}"{/if}
                class="l{$level} {if $qtde_filhos gt 0}non-leaf{/if}"
                {if !$setor->getDisponivelTramite() && !isset($isCadastroSetores) } disabled="disabled" data-toggle="tooltip" title="Setor indisponível para trâmite" {/if}>{$setor->getNome()}
        </option>
        {if $qtde_filhos gt 0}
            {option_setor setores=$filhos setor_pai_id=$setor->getId() level=$level+1 setor_sel=$setor_sel}
        {/if}
    {/foreach}
{/function}
{option_setor setores=$setores setor_pai_id=null setor_sel=$setor_selecionado}
