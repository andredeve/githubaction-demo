<h6 class="title">Informações</h6>
<table class="table">
    <tr>
        <th class="col-2">Status:</th>
        <td class="col-10">{$solicitacao->getStatus()}</td>
    </tr>
    <tr>
        <th class="col-2">Data:</th>
        <td class="col-10">{$solicitacao->getData()->format("d/m/Y")}</td>
    </tr>
    <tr>
        <th class="col-2">Processo:</th>
        <td class="col-10">
            <a href="{$app_url}processo/editar/id/{$anexo_anterior->getProcesso()->getId()}" target="_blank">
                {$anexo_anterior->getProcesso()->getNumero()}/{$anexo_anterior->getProcesso()->getExercicio()}
            </a>
        </td>
    </tr>
    <tr>
        <th class="col-2">Documento:</th>
        <td class="col-10">
            <a href="{$anexo_anterior->getArquivoUrl()}" target="_blank">
                {if empty($anexo_anterior->getNumero())}
                    Número Indefinido
                {else}
                    {$anexo_anterior->getNumero()}/{$anexo_anterior->getExercicio()}
                {/if}
            </a>
        </td>
    </tr>
    <tr>
        <th class="col-2">Solicitante:</th>
        <td class="col-10">{$solicitacao->getSolicitante()}</td>
    </tr>
    <tr>
        <th class="col-2">Motivo:</th>
        <td class="col-10">{$solicitacao->getMotivo()}</td>
    </tr>
</table>
{if $solicitacao->getTipo() eq "Edição"}
    <h6>Alterações</h6>
    <table class="table">
        <tr>
            <th></th><th>Antes</th><th>Depois</th>
        </tr>
        <tr>
            <th class="col-2">Data:</th><td>{$anexo_anterior->getData(true)}</td><td>{if !is_null($anexo_novo)}{$anexo_novo->getData()->format('d/m/Y')}{/if}</td>
        </tr>
        <tr>
            <th class="col-2">Número:</th><td>{$anexo_anterior->getNumero()}</td><td>{if !is_null($anexo_novo)}{$anexo_novo->getNumero()}{/if}</td>
        </tr>
        <tr>
            <th class="col-2">Tipo:</th><td>{$anexo_anterior->getDescricao()}</td><td>{if !is_null($anexo_novo)}{$anexo_novo->getDescricao()}{/if}</td>
        </tr>
        <tr>
            <th class="col-2">Classificação:</th>
            <td>
                {if $anexo_anterior->getClassificacao()}
                    {$anexo_anterior->getClassificacao()->getTitulo()}
                {/if}
            </td>
            <td>
                {if !is_null($anexo_novo) && $anexo_novo->getClassificacao() }
                    {$anexo_novo->getClassificacao()->getTitulo()}
                {/if}
            </td>
        </tr>
        <tr>
            <th class="col-2">Valor:</th><td>{$anexo_anterior->getValor(true)}</td><td>{if !is_null($anexo_novo)}{$anexo_novo->getValor(true)}{/if}</td>
        </tr>
        <tr>
            <th class="col-2">Páginas:</th><td>{$anexo_anterior->getQtdePaginas()}</td><td>{if !is_null($anexo_novo)}{$anexo_novo->getQtdePaginas()}{/if}</td>
        </tr>
        <tr>
            <th class="col-2">Descrição:</th><td>{$anexo_anterior->getDescricao()}</td><td>{if !is_null($anexo_novo)}{$anexo_novo->getDescricao()}{/if}</td>
        </tr>
        <tr>
            <th class="col-2">Arquivo:</th>
            <td class="col-5"><a href="{$anexo_anterior->getArquivoUrl()}" target="_blank"><span class="fa fa-file-pdf-o"></span> Ver arquivo original</a></td>
            <td class="col-5">{if !is_null($anexo_novo)}<a href="{$anexo_novo->getArquivoUrl()}" target="_blank"><span class="fa fa-file-pdf-o"></span> Ver novo arquivo</a>{/if}</td>
        </tr>
    </table>
{/if}
<div class="col text-center">
    {if $solicitacao->getStatus() eq "Pendente"}
        <button class="btn btn-primary btn-solicitacao-aprovar" data-solicitacao-id="{$solicitacao->getId()}" onclick="aprovarSolicitacaoAnexo(this)">Aprovar</button>
        <button class="btn btn-danger ml-4 mr-4 btn-solicitacao-reprovar" data-solicitacao-id="{$solicitacao->getId()}" onclick="reprovarSolicitacaoAnexo(this)">Recusar</button>
    {else}
        <button class="btn btn-primary disabled" disabled>Aprovar</button>
        <button class="btn btn-danger ml-4 mr-4 disabled" disabled>Recusar</button>
    {/if}
    <button class="btn btn-outline-secondary" data-dismiss="modal">Voltar</button>
</div>