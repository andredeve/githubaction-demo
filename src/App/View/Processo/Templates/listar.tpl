<table class="table table-bordered table-sm table-hover tabelaListaProcessos"
    {if isset($parametros_processo)}
        url="{$app_url}src/App/Ajax/Processo/listar_resumido.php?{$parametros_processo}"
        style="display: none"
    {/if}>
    <thead class="bg-light">
        <tr>
            {if isset($parametros_processo)}
                <th></th>
                <th class="text-center align-middle">Número</th>
                <th class="text-center align-middle">Exercício</th>
                <th class="text-left align-middle">Assunto</th>
                <th class="text-left align-middle">Interessado</th>
                <th class="text-left align-middle">Setor Atual</th>
                <th class="text-center align-middle">Abertura</th>
                <th class="text-center">Vencimento <br> 
                    <small class=" text-muted">Trâmite</small>
                </th>
                
                <th class="hidden">Objeto</th>
                <th class="hidden">Setor Origem</th>
                <th class="text-center" title="Fim vigência do {$nomenclatura}">Vigência<br> 
                    <small class=" text-muted">{$nomenclatura}</small>
                </th>
            {else}
                <th class="text-center"><i class="fa fa-paperclip"></i></th>
                <th class="text-center">Processo</th>
                <th class="text-left">Assunto</th>
                <th class="text-left">Interessado</th>
                <th class="text-left">Setor Atual</th>
                <th class="text-center">Abertura</th>
            {/if}
        </tr>
    </thead>
    <tbody>
    {if isset($resultado)}
        {foreach $resultado as $processo}
            <tr class="linha-processo" id="visualizar:{$processo->getId()}" title="{$processo->getObjeto()}">
                <td class="col-actions text-center vertical-middle">
                    {$anexos=$processo->getAnexos()}
                    {$qtde_anexos=count($anexos)}
                    {if $qtde_anexos gt 0}
                        <div class="btn-group">
                            <button title="{$qtde_anexos} anexo(s) encontrado(s)" type="button"
                                    class="btn btn-outline-info btn-xs dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-paperclip"></i>
                            </button>
                            <div class="dropdown-menu">
                                {foreach $processo->getAnexos() as $i=>$anexo}
                                    {if $anexo->getArquivo() neq null && strtolower($anexo->getExtensao()) eq 'pdf'}
                                        <a target="_blank" href="{$anexo->getPathUrl()}{$anexo->getArquivo()}"
                                           class="dropdown-item"><i class='fa fa-search'></i> {$anexo}</a>
                                    {elseif $anexo->getArquivo() neq null && $anexo->isImage() eq true}
                                        <a data-title="{$anexo}" data-lightbox="anexo_{$anexo->getId()}"
                                           href="{$anexo->getPathUrl()}{$anexo->getArquivo()}" class="dropdown-item"><i
                                                    class='fa fa-search'></i> {$anexo}</a>
                                    {elseif $anexo->getArquivo() neq null}
                                        <a class='dropdown-item' title='Visualizar Arquivos' target='_blank'
                                           href='{$app_url}src/App/View/Anexo/visualizar_digitalizados.php?anexo_id={$anexo->getId()}&indice={$i}&imagens={$anexo->getImagens(true)}'><i
                                                    class='fa fa-search'></i> {$anexo}</a>
                                    {else}
                                        <a class='dropdown-item'
                                           href='#'><i class='fa fa-search'></i> {$anexo}</a>
                                    {/if}
                                {/foreach}
                            </div>
                        </div>
                    {else}
                        -
                    {/if}
                </td>
                <td class="text-center bg-light">{$processo}</td>
                <td>{$processo->getAssunto()->getDescricao()}</td>
                <td>{$processo->getInteressado()->getPessoa()->getNome()}</td>
                <td>{$processo->getSetorAtual()}</td>
                <td class="text-center">{$processo->getDataAbertura(true)}</td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="6" class="text-muted">*Nenhum processo encontrado.</td>
            </tr>
        {/foreach}
    {/if}
    </tbody>
</table>
  