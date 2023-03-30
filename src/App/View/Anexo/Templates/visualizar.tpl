{foreach $anexos as $anexo}
    <tr class="linha-anexo">
        <td>{$anexo->getTipo()}</td>
        <td>{$anexo->getDescricao()}</td>
        <td class="text-center">{$anexo->getData()->format('d/m/Y')}</td>
        <td class="text-center">{$anexo->getNumero()}</td>
        <td class="text-center">
            {if $anexo->getArquivo() neq null}
                <a target="_blank" href="{$app_url}_files/processos/{$processo->getExercicio()}/{$processo->getNumero()}/{$anexo->getArquivo()}" class="btn btn-xs btn-warning"><i class='fa fa-search'></i></a> 
                {else}
                <a class='btn btn-warning btn-xs' title='Visualizar Arquivos' target='_blank' href='{$app_url}src/App/View/Anexo/visualizar_digitalizados.php?imagens={$anexo->getImagens(true)}'><i class='fa fa-search'></i></a>
                {/if}
            <a anexo_id="{$anexo->getId()}" href="#" nome_arquivo="{$anexo->getArquivo()}" title="Remover anexo" class="btn btn-xs btn-danger btn-excluir-anexo"><i class="fa fa-trash"></i></a>
        </td>
    </tr>
{foreachelse}
    <tr id="linha_empty_anexo">
        <td colspan="6" class="text-muted">*Nenhum anexo adicionado.</td>
    </tr>
{/foreach}


