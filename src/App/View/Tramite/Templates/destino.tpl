{if isset($tramite) and $tramite != null and $tramite->getForaFluxograma() eq false}
    {include file="requisitos.tpl"}
{/if}
<table id="tabelaDetalheTramite" class="table table-sm table-bordered" {if $usuarioIsExterno}style="display: none" {/if}>
    <thead class="bg-light">
    <tr>
        {if isset($setor_origem)}
            <th class="vertical-middle text-center">Setor Atual</th>
        {/if}
        <th class="vertical-middle text-center">Setor Destino</th>
        <th class="text-center align-middle">
            Status
        </th>
        <th class="text-center align-middle">Usuário Destino <br/>
            <span class="text-muted">*Se selecionado, somente ele poderá interagir com o protocolo.</span>
        </th>
        <th class="text-center align-middle">Prazo Setor Destino</th>
    </tr>
    </thead>
    <tbody>
    {if isset($setores_fase)}
        {$qtde_linhas=count($setores_fase)}
        {foreach $setores_fase as $i=>$setor_fase}
            {$setor=$setor_fase->getSetor()}
            <input type="hidden" class="setor_destino_fluxograma_id" name="setor_destino_fluxograma_id[{$i}]"
                   value="{$setor->getId()}"/>
            {include file="../../Tramite/Templates/linha_envio.tpl" numero_fase="{$numero_fase}" prazo_destino=$setor_fase->getVencimento() setor_origem=$setor_origem}
        {/foreach}
    {else}
        {if isset($setores) and count($setores) > 0}
            {include file="../../Tramite/Templates/linha_envio.tpl" i=0 prazo_destino="" qtde_linhas=1 setores=$setores setor_origem=$setor_origem}
        {else}
            {include file="../../Tramite/Templates/linha_envio.tpl" i=0 prazo_destino="" qtde_linhas=1 setor=null setor_origem=$setor_origem}
        {/if}
    {/if}
    </tbody>
</table>