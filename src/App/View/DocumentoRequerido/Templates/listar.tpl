{foreach $tramiteCadastro->getDocumentosRequerimentosCadastrados() as $documentoRequerido }

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
                    <a class="dropdown-item btn-editar-documento-requerido" documento-requerido="{$documentoRequerido->getId()}"  tramite="{$tramiteCadastro->getId()}" title="Editar" href="javascript:"><i class="fa fa-edit"></i> Editar</a>
                    <a  class="dropdown-item btn-excluir" title="Excluir" href="{$app_url}DocumentoRequerido/excluir/id/{$documentoRequerido->getId()}"><i class="fa fa-trash-o"></i> Excluir</a>
                </div>
            </div>
        </td>
    </tr>
{/foreach}