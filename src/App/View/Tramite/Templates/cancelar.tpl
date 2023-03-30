<div class="alert alert-warning">
    Ao realizar o encaminhamento para o setor de destino, o envio abaixo será cancelado.
</div>
<table class="table table-sm table-bordered">
    <thead>
    <tr>
        <td>Data/Hora envio</td>
        <td>Setor Atual</td>
        <td>Status</td>
        <td>Parecer</td>
        <td>Usuario Destino</td>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{$tramite->getDataEnvio(true,true)}</td>
        <td>{$tramite->getSetorAtual()}</td>
        <td>{$tramite->getStatus()}</td>
        <td>{$tramite->getParecer()}</td>
        <td>{$tramite->getUsuarioDestino()}</td>
    </tr>
    </tbody>
</table>
<div class="form-group">
    <label class="col-form-label">Justificativa de Cancelamento:</label>
    <textarea name="justificativaCancelamento" class="form-control" required></textarea>
</div>