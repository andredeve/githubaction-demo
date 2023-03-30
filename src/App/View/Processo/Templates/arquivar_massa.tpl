<form id="arquivarProcessoMassaForm" method="POST" class="form-horizontal" action="{$app_url}processo/arquivar">
    <div class="row">
        <div class="col-2">
            <table class="table table-bordered" style="max-height: 440px;overflow-y: scroll">
                <thead class="thead-light">
                <tr>
                    <th></th>
                    <th class="text-center vertical-middle">{$nomenclatura}</th>
                </tr>
                </thead>
                <tbody>
                {foreach $processos as $processo}
                    <tr title="{$processo->getObjeto()}">
                        <td class="text-center vertical-middle">
                            <input type="checkbox" name="processo_id[]" value="{$processo->getId()}" checked/>
                        </td>
                        <td class="vertical-middle text-center bg-light">
                            {$processo}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="required">Motivo do arquivamento:</label>
                        <textarea name="justificativa" class="form-control" rows="3" required="true"></textarea>
                        <small class="form-text text-muted">Descreva o porquê que os processos estão sendo arquivados
                            neste
                            momento.
                        </small>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#"><i class="fa fa-folder-open-o"></i> Localização
                                        Física</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            {include file="../../Processo/Templates/localizacao.tpl"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    <div class="form-group text-right">
        <button type="submit" data-style="expand-right" class="btn btn-primary ladda-button"><i
                    class="fa fa-archive"></i> Arquivar
        </button>
        <a href="#" class="btn btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                    class="fa fa-times"></i> Cancelar</a>
    </div>
</form>

