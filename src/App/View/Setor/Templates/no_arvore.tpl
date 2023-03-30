<ul>
    {foreach $setores as $setor}
        {if $setor->getIsAtivo() eq true}
            <li  rel="" data-jstree='{if in_array($setor->getId(), $setores_permitidos)}{literal}{"selected" : true}{/literal}{/if}' id="{$setor->getId()}" 
                 classe="{get_class($setor)}"
                 class="{if count($setor->getSetoresFilhos()) gt 0 }jstree-closed{/if} {if $setor->temFilhoMarcado($setores_permitidos, $setor)}jstree-open{/if}">
                {$setor->getNome()}
            </li>
        {/if}
    {/foreach}
</ul>

