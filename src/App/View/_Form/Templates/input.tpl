{if isset($label)}
    <label class="col-form-label">{$label}:</label>
{/if}
<input type="{if isset($type)}{$type}{else}text{/if}" autocomplete="off"
       class="form-control form-control-sm {if isset($class)}{$class}{else}{/if}"
       name="{$name}"
       value="{$value}"
       {if isset($id)}id="{$id}"{/if}
        {if isset($required)}required{/if}
        {if isset($readonly)}readonly="true"{/if}/>
