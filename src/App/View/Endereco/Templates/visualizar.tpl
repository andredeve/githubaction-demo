<table class="table table-sm table-striped">
    <tr>
        <td>Endereco:</td><td>{$endereco->getRua()}, {$endereco->getNumero()}</td>
    </tr>
    <tr>
        <td>Bairro:</td><td>{$endereco->getBairro()}</td>
    </tr>
    <tr>
        <td>Cidade:</td><td>{$endereco->getCidade()}</td>
    </tr>
    <tr>
        <td>Estado:</td><td>{$endereco->getEstado()}</td>
    </tr>
    <tr>
        <td>Complemento:</td><td>{$endereco->getComplemento()}</td>
    </tr>
</table>
<a target="_blank" href="http://www.google.com.br/maps/place/{$endereco}"><i class="fa fa-map-marker col-md-offset-2"></i> Ver localização no mapa</a>
