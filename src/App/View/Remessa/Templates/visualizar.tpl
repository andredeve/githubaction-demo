<table class="table">
    <tr>
        <td>Data:</td>
        <td>{$remessa->getData()}</td>
    </tr>
    <tr>
        <td>Hora:</td>
        <td>{$remessa->getHora()}</td>
    </tr>
    <tr>
        <td>Setor Origem:</td>
        <td>{$remessa->getSetorOrigem()}</td>
    </tr>
    <tr>
        <td>Responsável Origem:</td>
        <td>{$remessa->getResponsavelOrigem()}</td>
    </tr>
    <tr>
        <td>Setor Destino:</td>
        <td>{$remessa->getSetorDestino()}</td>
    </tr>
    <tr>
        <td>Responsável Destino:</td>
        <td>{$remessa->getResponsavelDestino()}</td>
    </tr>
</table>
{$processos=$remessa->getProcessos()}
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#">Processos <span class="badge badge-info">{count($processos)}</span></a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered table-hover" style="font-size: smaller;font-weight: lighter">
            <thead>
            <tr>
                <th>Processo</th>
                <th>Assunto</th>
                <th>Interessado</th>
            </tr>
            </thead>
            <tbody>
            {foreach $processos as $processo}
                <tr title="{$processo->getObjeto()}">
                    <td>{$processo}</td>
                    <td>{$processo->getAssunto()}</td>
                    <td>{$processo->getInteressado()}</td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="3" class="text-muted">Nenhum processo encontrado.</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
<hr/>
<div class="text-right">
    <a href="{$app_url}remessa/imprimir/{$remessa->getId()}" target="_blank" class="btn btn-warning"><i class="fa fa-print"></i>
        Imprimir</a>
</div>