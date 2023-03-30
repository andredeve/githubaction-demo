<table class="table table-sm table-striped table-bordered">
    <thead>
    <tr class="bg-light">
        <th class="hidden"></th>
        <th>Categoria</th>
        <th class="text-center">Número</th>
        <th class="text-center">Ano</th>
        <th class="text-center">Data</th>
        <th class="text-center">Qtde. dias</th>
        <th class="text-center">Vencimento</th>
        <th class="text-center">
            {if !isset($consultar)}
                <button objeto_ref_id="{$processo->getId()}" entidade="Documento"
                        title="Cadastra um  novo vencimento de documento vinculado a este processo"
                        class="btn btn-success btn-sm btn-cadastrar-entidade" type="button"><i
                            class="fa fa-plus"></i> Novo
                </button>
            {/if}
        </th>
    </tr>
    </thead>
    <tbody>
    {foreach $documentos->toArray() as $i=>$documento}
        <tr title="{$documento->getObservacoes()}">
            <td class="hidden">{$documento->getId()}</td>
            <td>{$documento->getCategoria()}</td>
            <td class="text-center">{$documento->getNumero()}</td>
            <td class="text-center">{$documento->getExercicio()}</td>
            <td class="text-center">{$documento->getData(true)}</td>
            <td class="text-center">{$documento->getDiasVencimento()}</td>
            <td class="text-center">{$documento->getVencimento(true)}</td>
            <td class="text-center vertical-middle">
                {if !isset($consultar)}
                    <button objeto_ref_id="{$processo->getId()}" entidade_id="{$documento->getId()}:{$i}"
                            entidade="Documento" type="button"
                            class="btn btn-info btn-xs btn-editar-entidade" title="Editar"><i
                                class="fa fa-edit"></i></button>
                    <a entidade="Documento" objeto_ref_id="{$processo->getId()}"
                       class="btn btn-danger btn-xs btn-excluir-entidade" title="Excluir"
                       href="{$app_url}documento/excluir/id/{$documento->getId()}"><i class="fa fa-times"></i></a>
                {/if}
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="7" class="text-muted">* Nenhum documento encontrado.</td>
        </tr>
    {/foreach}
    </tbody>
</table>