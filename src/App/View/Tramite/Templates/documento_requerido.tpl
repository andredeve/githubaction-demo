<fieldset>
    <legend>Requerer documentação:
        <i data-toggle="popover" data-html="true" title="Essa funcionalidade vai solicitar/bloquear o próximo usuário de tramitar o processo sem o preenchimento dos dados requeridos abaixo. " class="fa fa-question-circle text-info tooltip-icon"></i> </legend>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th colspan="3" class="text-center col-8">Anexo</th>
            <th rowspan="2" class="text-center vertical-middle">Obrigatório? </th>
            <th rowspan="2" class="text-center vertical-middle">Assinatura Obrigatória? </th>
            <th rowspan="2" class="text-center vertical-middle">
                <button type="button" class="btn btn-sm btn-success btn-documento-requerido"
                        {if isset($tramite)}tramite="{$tramite->getId()}"{/if}
                        data-pode-assinar={if isset($pode_assinar) && $pode_assinar}"true"{else}"false"{/if}
                >
                    <i class="fa fa-plus"></i>
                </button>
            </th>
        </tr>
        <tr>
            <th class="text-center" >Tipo</th>
            <th class="text-center">Descrição</th>
            <th  class="text-center col-2">Número</th>
        </tr>
        </thead>
        <tbody id="tbodyDocumentosRequeridos">
        {foreach $tramite->getDocumentosRequerimentosCadastrados() as $documentoRequerido }
            <tr>
                <td>{$documentoRequerido->getAnexo()->getTipo()}</td>
                <td>{$documentoRequerido->getAnexo()->getDescricao()}</td>
                <td>{$documentoRequerido->getAnexo()->getNumero()}</td>
                <td>
                    {if $documentoRequerido->getIsObrigatorio()}
                        Sim
                    {else}
                        Não
                    {/if}
                </td>
                <td>
                    {if $documentoRequerido->getIsAssinaturaObrigatoria()}
                        Sim
                    {else}
                        Não
                    {/if}
                </td>
                <td class=" col-actions vertical-middle">
                    <div class="btn-group dropleft">
                        <button title="Ações disponíveis" type="button" class="btn btn-light border btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  <i class="fa fa-cogs"></i></button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item btn-editar-documento-requerido" documento-requerido="{$documentoRequerido->getId()}"  tramite="{$tramite->getId()}" title="Editar" href="javascript:"><i class="fa fa-edit"></i> Editar</a>
                            <a  class="dropdown-item btn-excluir" title="Excluir" href="{$app_url}DocumentoRequerido/excluir/id/{$documentoRequerido->getId()}"><i class="fa fa-trash-o"></i> Excluir</a>
                        </div>
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</fieldset>