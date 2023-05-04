<div class="alert alert-success">
    {if !$usuarioEhInteressado}
        <h3>{$parametros['nomenclatura']} criado com sucesso!</h3>
        <span class="lead">{$parametros['nomenclatura']} Nº {$processo}</span><br/>
    {else}
        <h3>Solicitação de {$parametros['nomenclatura']} criada com sucesso!</h3>
    {/if}
    <span class="lead">Criado em {$processo->getDataAbertura()->format('d/m/Y')}  às {$processo->getDataAbertura()->format('H:i')}</span><br/>
</div>
<hr/>
<div>
    {if $usuarioEhInteressado}
        <a href="{$app_url}" class="btn btn-primary"><i class="fa fa-home"></i> Página Inicial</a>
        <a href="{$app_url}Contribuinte/cadastrar" class="btn btn-success"><i class="fa fa-plus"></i> Novo {$parametros['nomenclatura']}</a>
        <a href="{$app_url}Contribuinte/editar/id/{$processo->getId()}" class="btn btn-primary"><i class="fa fa-search"></i> Visualizar Solicitação de {$parametros['nomenclatura']}</a>
    {else}
        <a title="Gerar capa para processo" target="_blank" href="{$app_url}Processo/gerarCapa/processo/{$processo->getId()}" class="btn btn-primary"><i class="fa fa-lg fa-file"></i> Gerar Capa</a>
        <a target="_blank" href="{$app_url}Processo/gerarGTE/processo/{$processo->getId()}/{$processo->getNumeroFase()}" class="btn btn-primary" title="Gerar Guia de tramitação eletrônica"><i class="fa fa-file-pdf-o"></i> Gerar GTE</a>
        <a target="_blank" href="{$app_url}Processo/gerarEtiqueta/processo/{$processo->getId()}" class="btn btn-primary"><i class="fa fa-th-list"></i> Gerar Etiqueta</a>
        <a target="_blank" href="{$app_url}Processo/gerarRecibo/processo/{$processo->getId()}" class="btn btn-primary"><i class="fa fa-file-text-o"></i> Gerar Recibo</a>
        <a href="{$app_url}Processo/editar/id/{$processo->getId()}" class="btn btn-primary"><i class="fa fa-search"></i> Visualizar {$parametros['nomenclatura']}</a>
        <a href="{$app_url}Processo/cadastrar" class="btn btn-success"><i class="fa fa-plus"></i> Novo {$parametros['nomenclatura']}</a>
    {/if}
</div>
