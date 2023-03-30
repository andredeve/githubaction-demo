<fieldset>
    <legend class="lead">
        <small class="text-info"><i class="fa fa-th-list"></i> Preencha os campos necessários do template.</small>
    </legend>
    <table class="table">
        {foreach $modelo->getVariaveis() as $variavel}
            <tr>
                <th class="w-25">{$variavel}:</th>
                <td>
                    {include file="../../_Form/Templates/input.tpl" type='text' value='' name=\Core\Util\Functions::sanitizeString($variavel) required=true class='documentModelInput'}
                </td>
            </tr>
        {/foreach}
    </table>
</fieldset>