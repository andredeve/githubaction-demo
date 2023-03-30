<div class="card-body tab-content">
    <div class="tab-pane fade show active" id="detalhadoTab" role="tabpanel">
        <table id="tabelaAnexosPorPeriodo" class="table table-bordered table-sm table-hover">
            <thead class="bg-light">
                <tr>
                    <th colspan="3" class="text-center">Referência</th>
                    <th colspan="5" class="text-center">Dados do Documento</th>
                    <th></th>
                </tr>
                <tr>
                    <th class="text-center">Processo</th>
                    <th>Anexador por</th>
                    <th class="text-center">Anexado em</th>
                    <th class="text-center">Data</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th class="text-center">Número</th>
                    <th class="text-center">Qtde. Páginas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {foreach $anexos as $anexo}
                    <tr>
                        <td class="text-center">
                            <a title="Visualizar Processo" href="#">{$anexo->getProcesso()}</a>
                        </td>
                        <td>{$anexo->getUsuario()}</td>
                        <td class="text-center">{$anexo->getDataCadastro()->format('d/m/Y - H:i')}</td>
                        <td class="text-center">{$anexo->getData()->format('d/m/Y')}</td>
                        <td>{$anexo->getTipo()}</td>
                        <td>{$anexo->getDescricao()}</td>
                        <td class="text-center">{$anexo->getNumero()}</td>
                        <td class="text-center">{$anexo->getQtdePaginas()}</td>
                        <td class="text-center">
                            {if $anexo->getArquivo() neq null && $anexo->getExtensao() eq 'pdf'}
                                <a target="_blank" href="{$anexo->getPathUrl()}{$anexo->getArquivo()}" class="btn btn-xs btn-info"><i class='fa fa-search'></i></a> 
                                {elseif $anexo->getArquivo() neq null && $anexo->isImage() eq true}
                                <a data-title="{$anexo}" data-lightbox="anexo_{$anexo->getId()}" href="{$anexo->getPathUrl()}{$anexo->getArquivo()}" class="btn btn-xs btn-info"><i class='fa fa-search'></i></a> 
                                {else}
                                <a class='btn btn-info btn-xs' title='Visualizar Arquivos' target='_blank' href='{$app_url}src/App/View/Anexo/visualizar_digitalizados.php?processo_id={$anexo->getProcesso()->getId()}&imagens={$anexo->getImagens(true)}'><i class='fa fa-search'></i></a>
                                {/if}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <div class="tab-pane fade" id="quantitativoTab" role="tabpanel">
        <table id="tabelaAnexoQuantitativo" class="table table-bordered table-sm table-hover">
            <thead class="bg-light">
                <tr>
                    <th >Tipo</th>
                    <th  class="text-center">Total de Documentos</th>
                    <th class="text-center">Total de Páginas</th>
                </tr>
            </thead>
            <tbody>
                {$totalDocumentos= 0}
                {$totlaPaginas = 0}
                {foreach $anexos_por_tipo as $anexo}
                    <tr>
                        <td>
                            {$totalDocumentos = $totalDocumentos + $anexo["qtde"]}
                            {$totlaPaginas = $totlaPaginas + $anexo["totalPaginas"]}
                            
                            {$anexo[0]->getTipo()}
                        </td>
                        <td class="text-center">{$anexo["qtde"]}</td>
                        <td class="text-center">{$anexo["totalPaginas"]}</td>                    
                    </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td class="text-center"><strong>{$totalDocumentos}</strong></td>
                    <td class="text-center"><strong>{$totlaPaginas}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<script defer type="text/javascript" src="{$app_url}assets/js/view/anexo/listar_relatorio.js?v={$file_version}"></script>