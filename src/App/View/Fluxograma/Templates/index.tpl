<div class="alert alert-warning text-justify">
    <i class="fa fa-info-circle"></i> <strong>Fluxograma de Processos</strong><br/>Os Fluxogramas de Processos são baseados em assuntos. Dentro de cada fluxograma, são definidas as fases do processo,
    em cada fase do processo, ele pode ser enviado para n setores, no qual cada um terá seu prazo. 
    O processo só será encaminhado para próxima fase quando todos os n setores concluirem suas atividades.
</div>
<a class="btn btn-primary btn-loading" href="{$app_url}fluxograma/cadastrar"><i class="fa fa-plus"></i> Novo</a>
<hr/>
<table class="table table-bordered table-hover datatable table-sm">
    <thead class="thead-light">
        <tr>
            <th>Cód.#</th>
            <th>Assunto</th>
            <th>Data Cadastro</th>
            <th>Última Alteração</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {foreach $fluxogramas as $fluxograma}
            <tr>
                <td>{$fluxograma->getId()}</td>
                <td class="text-left">{$fluxograma->getAssunto()->getDescricao()}</td>
                <td class="text-center">
                    {$fluxograma->getDataCadastro()->format('d/m/Y')}
                </td>
                <td>
                    {if $fluxograma->getUltimaAlteracao() neq ""}
                        {$fluxograma->getUltimaAlteracao()->format('d/m/Y  - H:i:s')}
                    {else}
                        Não registrado
                    {/if}
                </td>
                <td>
                    <a class="btn btn-warning btn-xs btn-loading" title="Gerenciar estrutura de requisitos entre trâmites" href="{$app_url}fluxograma/estrutura/id/{$fluxograma->getId()}"><i class="fa fa-cogs"></i></a>
                    <a class="btn btn-info btn-xs btn-loading" title="Editar" href="{$app_url}fluxograma/editar/id/{$fluxograma->getId()}"><i class="fa fa-edit"></i></a>
                    <a class="btn btn-danger btn-xs btn-excluir" title="Excluir" href="{$app_url}fluxograma/excluir/id/{$fluxograma->getId()}"><i class="fa fa-times"></i></a>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>