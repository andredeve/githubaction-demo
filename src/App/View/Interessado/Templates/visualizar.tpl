<h6 class="text-info">{$interessado->getPessoa()->getNome()}</h6>
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#pessoal" role="tab" aria-controls="pessoal">
                    <i class="fa fa-user-circle-o"></i> Dados Pessoais
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#contato" role="tab" aria-controls="contato">
                    <i class="fa fa-phone"></i> Contato
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#endereco" role="tab" aria-controls="endereco">
                    <i class="fa fa-map-marker"></i> Endereço
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane active" id="pessoal" role="tabpanel">
                <table class="table table-sm table-striped">
                    {if $interessado->getPessoa()->getTipoPessoa() eq \App\Enum\TipoPessoa::FISICA}
                        <tr>
                            <td>Sexo:</td>
                            <td>{$interessado->getPessoa()->getSexo(true)}</td>
                        </tr>
                        <tr>
                            <td>Idade (anos):</td>
                            <td>{$interessado->getIdade()}</td>
                        </tr>
                        <tr>
                            <td>Data Nascimento:</td>
                            <td>{$interessado->getPessoa()->getDataNascimento(true)}</td>
                        </tr>
                        <tr>
                            <td>C.P.F:</td>
                            <td>{$interessado->getPessoa()->getPessoa()->getCpf()}</td>
                        </tr>
                        <tr>
                            <td>R.G.:</td>
                            <td>{$interessado->getPessoa()->getRg()}</td>
                        </tr>
                        <tr>
                            <td>Nacionalidade:</td>
                            <td>{$interessado->getPessoa()->getNacionalidade()}</td>
                        </tr>
                        <tr>
                            <td>Estado Civil:</td>
                            <td>{$interessado->getPessoa()->getEstadoCivil(true)}</td>
                        </tr>
                    {else}
                        <tr>
                            <td>C.N.P.J.:</td>
                            <td>{$interessado->getPessoa()->getCnpj()}</td>
                        </tr>
                        <tr>
                            <td>I.E.:</td>
                            <td>{$interessado->getPessoa()->getIe()}</td>
                        </tr>
                    {/if}
                </table>
            </div>
            <div class="tab-pane" id="contato" role="tabpanel">
                <table class="table table-sm table-striped">
                    <tr>
                        <td>E-mail:</td>
                        <td>{$interessado->getPessoa()->getEmail()}</td>
                    </tr>
                    <tr>
                        <td>Telefone:</td>
                        <td>{$interessado->getPessoa()->getTelefone()}</td>
                    </tr>
                    <tr>
                        <td>Celular:</td>
                        <td>{$interessado->getPessoa()->getCelular()}</td>
                    </tr>
                </table>
            </div>
            <div class="tab-pane" id="endereco" role="tabpanel">
                {$endereco=$interessado->getPessoa()->getEndereco()}
                {include file="../../Endereco/Templates/visualizar.tpl"}
            </div>
        </div>
    </div>
</div>
<br/>
<div class="text-right">
    <a class="btn btn-info btn-sm btn-loading" title="Editar"
       href="{$app_url}interessado/editar/id/{$interessado->getId()}"><i class="fa fa-edit"></i> Editar</a>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i>
        Fechar
    </button>
</div>
