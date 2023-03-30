{if $modal eq true && $usuarioEhInteressado eq false}
    <h4 class='modal-title-dinamic'>{$page_title}</h4>
{/if}
<form id="{$entidade}Form" method="POST" action="{$app_url}interessado/{$acao}" class="form-horizontal">
    <input type="hidden" name="entidade" value="{$entidade}" />
    <input type="hidden" id="isExterno" name="isExterno" value="{$usuarioEhInteressado}" />
    <input type="hidden" name="id" value="{$interessado->getId()}" />
    <input type="hidden" name="pessoa_id" value="{$interessado->getPessoa()->getId()}" />
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class=" active nav-link" data-toggle="tab" href="#pessoal" role="tab" aria-controls="pessoal">
                        <i class="fa fa-user-circle-o text-warning"></i> Dados Pessoais
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#contato" role="tab" aria-controls="contato">
                        <i class="fa fa-map-marker text-warning"></i> Endereço
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content card-body">
            <div class="tab-pane fade show active" id="pessoal" role="tabpanel">

                <div class="col-2 col-xs-12" {if $usuarioEhInteressado == true}style="display: none" {/if}>
                    <label class="col-form-label">Ativo?</label><br />
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline1" name="isAtivo" value="1"
                            class="custom-control-input" {if $interessado->getIsAtivo() eq true}checked{/if}>
                        <label class="custom-control-label" for="customRadioInline1">Sim</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="customRadioInline2" name="isAtivo" value="0"
                            class="custom-control-input" {if $interessado->getIsAtivo() eq false}checked{/if}>
                        <label class="custom-control-label" for="customRadioInline2">Não</label>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3 col-sm-12">
                        <label class="col-form-label required">Tipo Pessoa:</label>
                        <div>
                            <select id="select_tipo_pessoa" name="tipoPessoa" class="form-control form-control-sm">
                                <option value="">Selecione</option>
                                {foreach \App\Enum\TipoPessoa::getOptions() as $value=>$text}
                                    <option value="{$value}"
                                        {if $interessado->getPessoa()->getTipo() eq $value}selected{/if}>{$text}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <label id="nome" class="col-form-label required">Nome:</label>
                        <input type="text" name="nome" autocomplete="false"
                            value="{$interessado->getPessoa()->getNome()}" autofocus="true" required="true"
                            class="form-control form-control-sm maiscula">
                    </div>
                </div>
                <div id="divPessoaFisica"
                    {if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::JURIDICA}style="display: none"
                    {/if}>
                    <div class="form-group row">
                        {if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::FISICA}
                            <div class="col-md-3 col-sm-12">
                                <label>Data Nascimento:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class='fa fa-calendar'></i>
                                        </div>
                                    </div>
                                    <input type="text" name="dataNascimento" id="dataNascimento"
                                        value="{if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::FISICA}{$interessado->getPessoa()->getDataNascimento(true)}{/if}"
                                        class="form-control form-control-sm datepicker">
                                </div>

                            </div>
                        {/if}
                        <div class="col-md-3 col-sm-12">
                            <label class="col-form-label">C.P.F:</label>
                            <input type="text" name="cpf" id="cpf"
                                value="{if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::FISICA}{$interessado->getPessoa()->getCpf()}{/if}"
                                class="form-control form-control-sm cpf"
                                {if $interessado->getPessoa()->getTipo() == 'fisica'}required{/if}>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <label>R.G:</label>
                            <input type="text" name="rg"
                                value="{if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::FISICA}{$interessado->getPessoa()->getRg()}{/if}"
                                class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <label>Sexo:</label>
                            <div>
                                <select name="sexo" class="form-control form-control-sm">
                                    <option value="">Selecione</option>
                                    {foreach \App\Enum\Sexo::getOptions() as $value=>$text}
                                        <option value="{$value}"
                                            {if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::FISICA}{if $interessado->getPessoa()->getSexo() eq $value}selected{/if}{/if}>
                                            {$text}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label">Nacionalidade:</label>
                            <input type="text" name="nacionalidade"
                                value="{if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::FISICA}{$interessado->getPessoa()->getNacionalidade()}{/if}"
                                class="form-control form-control-sm maiscula">
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <label class="col-form-label">Estado Civil:</label>
                            <div>
                                <select name="estadoCivil" class="form-control form-control-sm">
                                    <option value="">Selecione</option>
                                    {foreach \App\Enum\EstadoCivil::getOptions() as $value=>$text}
                                        <option value="{$value}"
                                            {if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::FISICA}{if $interessado->getPessoa()->getEstadoCivil() eq $value}selected{/if}{/if}>
                                            {$text}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="divPessoaJuridica"
                    {if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::FISICA}style="display: none"
                    {/if}>
                    <div class="form-group row">
                        <div class="col">
                            <label class="col-form-label required">C.N.P.J:</label>
                            <input onblur="validaCNPJ(this,event);" type="text" id="cnpj" name="cnpj"
                                autocomplete="false" autocomplete="false"
                                value="{if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::JURIDICA}{$interessado->getPessoa()->getCnpj()}{/if}"
                                class="form-control form-control-sm cnpj"
                                {if $interessado->getPessoa()->getTipo() == 'juridica'}required{/if}>
                        </div>
                        <div class="col">
                            <label class="col-form-label">I.E:</label>
                            <input type="text" name="ie" autocomplete="false"
                                value="{if $interessado->getPessoa()->getTipo() eq \App\Enum\TipoPessoa::JURIDICA}{$interessado->getPessoa()->getIe()}{/if}"
                                class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6 col-sm-12">
                        <label class="col-form-label">E-mail:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class='fa fa-envelope-o'></i>
                                </div>
                            </div>
                            <input type="email" name="email" autocomplete="false"
                                value="{$interessado->getPessoa()->getEmail()}" class="form-control form-control-sm"
                                required="true">
                        </div>
                    </div>
                    <div class="col">
                        <label>Telefone:</label>
                        <input type="text" autocomplete="false" name="telefone"
                            value="{$interessado->getPessoa()->getTelefone()}"
                            class="form-control form-control-sm phone_with_ddd">
                    </div>
                    <div class="col">
                        <label>Celular:</label>
                        <input type="text" autocomplete="false" name="celular"
                            value="{$interessado->getPessoa()->getCelular()}"
                            class="form-control form-control-sm telefone" {if $usuarioEhInteressado}required="true"
                            {/if}>
                    </div>
                </div>
                {if $usuarioEhInteressado}
                    <div class="form-group row">
                        <div class="col-md-6 col-sm-12">
                            <label class="col-form-label">Senha:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class='fa fa-lock'></i>
                                    </div>
                                </div>
                                <input type="password" name="senha" id="senha" autocomplete="false"
                                    class="form-control form-control-sm" required="true">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <label class="col-form-label">Confirma Senha:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class='fa fa-lock'></i>
                                    </div>
                                </div>
                                <input type="password" name="senha_confirma" id="senha_confirma" autocomplete="false"
                                    class="form-control form-control-sm" required="true">
                            </div>
                        </div>
                    </div>
                {/if}

            </div>
            <div class="tab-pane fade" id="contato" role="tabpanel">
                {$objeto=$interessado->getPessoa()}
                {include file="../../Endereco/Templates/endereco.tpl"}
            </div>
        </div>
    </div>
    <hr />
    <div class="form-group row">
        <div class="col ml-auto">
            <button type="submit" class="btn btn-primary ladda-button"><i class="fa fa-save"></i> Salvar</button>
            {if !isset($modal) || $modal eq false}
                <a class="btn btn-light border btn-loading"
                    href="{$app_url}{if $usuarioEhInteressado}login{else}{$entidade}{/if}">
                    <i class="fa fa-times"></i>Cancelar
                </a>
            {else}
                <a href="#" class="btn  btn-light border" onclick="$(this).closest('.modal').modal('hide');"><i
                        class="fa fa-times"></i> Cancelar</a>
            {/if}

        </div>
        <div class="col-md-4 mr-auto text-right">
            {if $interessado->getId() neq ""}
                <p class="form-control-static text-muted">
                    Data cadastro registro: {$interessado->getDataCadastro()->format('d/m/Y')}<br />
                    Última alteração:
                    {if $interessado->getUltimaAlteracao() neq ""}
                        {$interessado->getUltimaAlteracao()->format('d/m/Y')} às
                        {$interessado->getUltimaAlteracao()->format('H:i')}
                    {else}
                        Não registrado
                    {/if}
                </p>
            {/if}
        </div>
    </div>
</form>