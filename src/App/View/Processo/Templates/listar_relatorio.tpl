<div class="table-responsive">
    <table style="display: none" groupColumn="{$groupColumn}"
           class="table table-bordered table-sm tabela-processos-relatorio table-hover">
        <thead class="bg-light">
        <tr>
            <th>Origem</th>
            <th>Número</th>
            <th>Exercício</th>
            <th>Abertura</th>
            <th>Assunto</th>
            <th>Interessado</th>
            <th>Setor Atual</th>
            <th>Responsável</th>
            <th>Data Trâmite</th>
            <th>Vencimento</th>
            <th>Status</th>
            <th class="hidden">Objeto</th>
        </tr>
        </thead>
        <tbody>
        {foreach $processos as $tramite}
            {$processo = $tramite->getProcesso()}
            <tr style="cursor: help">
                <td>{$processo->getOrigem(true)}</td>
                <td>{$processo}</td>
                <td>{$processo->getExercicio()}</td>
                <td>{$processo->getDataAbertura(true)}</td>
                <td>
                    {if $processo->getAssunto()} 
                        {$processo->getAssunto(true)}
                    {/if}
                </td>
                <td>{$processo->getInteressado(true)}</td>
                <td>
                    {if $tramite->getSetorAtual()}
                        {$tramite->getSetorAtual()->getNome()}
                    {/if}
                </td>
                <td>
                    {if $tramite->getResponsavel() }
                        {$tramite->getResponsavel()} 
                    {/if}
                </td>
                <td>{$tramite->getDataEnvio(true)}</td>
                <td>
                {if $tramite->getDataVencimento() instanceof DateTime }
                    {$tramite->getDataVencimento()->format('d/m/Y')}
                {/if}
                </td>
                <td>{$tramite->getStatus()}</td>
                <td class="hidden">{$processo->getObjeto()}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>