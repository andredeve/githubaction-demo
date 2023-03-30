{if isset($assunto) and $assunto->getFluxograma() neq null}
    {$fase=$assunto->getFluxograma()->getFases($numero_fase)}
    {if $fase neq null}
        {if isset($setor_id) and !empty($setor_id) }
            {$setores_fase=$fase->getSetoresFase($setor_id)}
        {else}
            {$setores_fase=$fase->getSetoresFase()}
        {/if}
        {foreach $setores_fase as $setor_fase}
            {if $setor_fase->temRequisitos() eq true}
                <div id="divRequisitos">
                    {if count($setor_fase->getCampos()) gt 0}
                        <div class="card">
                            <div class="card-body p-2">
                                {foreach $setor_fase->getCampos() as $campo}
                                    {if $campo->isAtivo() eq true}
                                        {if $campo->getTipo() eq App\Enum\TipoCampo::ARQUIVO}
                                            <fieldset {if $campo->getIsObrigatorio() eq true} is_obrigatorio="true" {/if}>
                                                <legend>
                                                    <small>
                                                        <i class="fa fa-file-text-o"></i> {$campo->getNome()}
                                                        <a href="javascript:" data-toggle="tooltip-html"
                                                           title="{$campo->getDescricao()}"><i
                                                                    class="fa fa-question-circle"></i></a>
                                                    </small>
                                                </legend>
                                                {if $campo->getAssinaturaObrigatoria() and !$usuarioEhInteressado}
                                                    <div class="form-group row">
                                                        <div class="col-3">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Data
                                                                Limite Assinatura:</label>
                                                            <input type="text" value=""
                                                                   name="data_assinatura_campo_{$campo->getId()}"
                                                                   class="form-control form-control-sm datepicker"
                                                                   {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                        </div>
                                                        {if $campo->getNumeroTemplateObrigatorio() eq true or $campo->getAssinaturaObrigatoria() eq true}
                                                            <div class="col-3">
                                                                <div style="display: flex;justify-content: space-between">
                                                                    <label class="col-form-label">Número:</label>
                                                                    <span class="col-form-label">
                                                                        Autonumerar?
                                                                        <input type="checkbox"
                                                                               class="ignore-validate"
                                                                               id="documento_auto_numeric_{$campo->getId()}"
                                                                               name="documento_auto_numeric_{$campo->getId()}"
                                                                               value="0"
                                                                               onchange="alternarEntradaNumeroAnexo(this,'numero_campo_{$campo->getId()}')"
                                                                        />
                                                                    </span>
                                                                </div>
                                                                <input type="number"
                                                                       name="numero_campo_{$campo->getId()}"
                                                                       id="numero_campo_{$campo->getId()}"
                                                                       class="form-control form-control-sm"
                                                                       {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                            </div>
                                                            <div class="col-3">
                                                                <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Ano:</label>
                                                                <input type="text" name="ano_campo_{$campo->getId()}"
                                                                       class="form-control form-control-sm ano"
                                                                       maxlength="4"
                                                                       onchange="validaAno($(this));"
                                                                       {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                            </div>
                                                            <div class="col-3">
                                                                <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Data
                                                                    Emissão:</label>
                                                                <input type="text" value=""
                                                                       name="data_campo_{$campo->getId()}"
                                                                       class="form-control form-control-sm datepicker"
                                                                       {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                            </div>
                                                        {/if}
                                                    </div>
                                                {/if}

                                                <div class="form-group row">
                                                    {if $campo->getAssinaturaObrigatoria() eq true}
                                                        <div class="col-6">
                                                            <label class="col-form-label">Grupos de Signatários:</label>
                                                            <select name="grupo_assinatura_campo_{$campo->getId()}[]"
                                                                    class="ignore-validate select2 form-control grupo_assinatura"
                                                                    data-campo-id="{$campo->getId()}" multiple
                                                            >
                                                                <option value="">Selecione</option>
                                                                {if isset($grupos) and !empty($grupos)}
                                                                    {foreach $grupos as $grupo}
                                                                        <option value="{$grupo->id}"
                                                                                data-toggle="tooltip"
                                                                                {if !empty($grupo->signatarios)}
                                                                                    title="{foreach $grupo->signatarios as $signatario} - {$signatario->nome} ({$signatario->cargo})&#010; {/foreach}"
                                                                                    data-signatarios="{$grupo->signatarios}"
                                                                                {/if}
                                                                                {if !empty($grupo->empresa)}
                                                                                    data-empresa="{$grupo->empresa->id}"
                                                                                {/if}
                                                                        >{$grupo->nome}</option>
                                                                    {/foreach}
                                                                {/if}
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Signatários:</label>
                                                            <select name="signatario_assinatura_campo_{$campo->getId()}[]"
                                                                    class="select2 form-control signatario_assinatura {if $campo->getAssinaturaObrigatoria() eq false}ignore-validate{/if}"
                                                                    multiple
                                                                    {if $campo->getIsObrigatorio() eq true}required{/if}>
                                                                <option value="">Selecione</option>
                                                                {if !empty($signatarios)}
                                                                    {foreach $signatarios as $signatario}
                                                                        <option value="{$signatario->id}"
                                                                                data-toggle="tooltip"
                                                                                title="{$signatario->nome}">
                                                                            {$signatario->nome}
                                                                        </option>
                                                                    {/foreach}
                                                                {/if}
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Empresa:</label>
                                                            <select id="empresa_campo_{$campo->getId()}"
                                                                    class="select2 form-control select_empresa select_empresa_{$campo->getId()} {if $campo->getAssinaturaObrigatoria() eq false}ignore-validate{/if}"
                                                                    name="empresa_campo_{$campo->getId()}"
                                                                    {if $campo->getIsObrigatorio() eq true}required{/if}
                                                            >
                                                                <option value="">Selecione</option>
                                                                {foreach $empresas as $item}
                                                                    <option value="{$item->id}" data-toggle="tooltip"
                                                                            {if $assinatura && $assinatura->getEmpresa() === $item->id}
                                                                                selected="selected"
                                                                            {/if}
                                                                    >
                                                                        {$item->nome}
                                                                    </option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Tipo
                                                                de Documento:</label>
                                                            <select id="tipo_documento_campo_{$campo->getId()}"
                                                                    class="select2 form-control select_tipo_documento select_tipo_documento_{$campo->getId()} {if $campo->getAssinaturaObrigatoria() eq false}ignore-validate{/if}"
                                                                    name="tipo_documento_campo_{$campo->getId()}"
                                                                    {if $campo->getIsObrigatorio() eq true}required={/if}>
                                                                <option value="">Selecione</option>
                                                                {foreach $tipos_documentos as $item}
                                                                    <option value="{$item->id}" data-toggle="tooltip"
                                                                            {if $assinatura && $assinatura->getTipoDocumento() === $item->id}
                                                                                selected="selected"
                                                                            {/if}
                                                                    >
                                                                        {$item->nome}
                                                                    </option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                    {/if}
                                                    <div class="col-12">
                                                        <label class="control-label {if $campo->getIsObrigatorio() eq true}required{/if}">Arquivo
                                                            (.pdf):</label>
                                                        {if $campo->getTemplate() neq null}
                                                            <a title="Ver template selecionado"
                                                               class="btn btn-link btn-sm float-right"
                                                               target="_blank"
                                                               href="{$app_url}_files/processos/templates/{$campo->getTemplate()}">
                                                                <i class="fa fa-file-word-o"></i> Download Template
                                                            </a>
                                                        {/if}
                                                        <input type="file" accept="application/pdf"
                                                               id="campo_{$campo->getId()}"
                                                               name="campo_{$campo->getId()}" class="fileinput"
                                                                {if $campo->getIsObrigatorio() eq true} required="true"{/if}>
                                                        <div id="campo_{$campo->getId()}-error"></div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        {elseif $campo->getTipo() eq App\Enum\TipoCampo::ARQUIVO_MULTIPLO}
                                            <fieldset>
                                                <legend>
                                                    <small>
                                                        <i class="fa fa-file-text-o"></i> {$campo->getNome()}
                                                        <a href="javascript:" data-toggle="tooltip-html"
                                                           title="{$campo->getDescricao()}"><i
                                                                    class="fa fa-question-circle"></i></a>
                                                    </small>
                                                </legend>
                                                {if $campo->getAssinaturaObrigatoria() and !$usuarioEhInteressado}
                                                    <div class="form-group row">
                                                        <div class="col-3">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Data
                                                                Limite Assinatura:</label>
                                                            <input type="text" value=""
                                                                   name="data_assinatura_campo_{$campo->getId()}"
                                                                   class="form-control form-control-sm datepicker"
                                                                   {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                        </div>
                                                        <div class="col-3">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Ano:</label>
                                                            <input type="text" name="ano_campo_{$campo->getId()}"
                                                                   class="form-control form-control-sm ano"
                                                                   maxlength="4"
                                                                   onchange="validaAno($(this));"
                                                                   {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                        </div>
                                                        <div class="col-3">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Data
                                                                Emissão:</label>
                                                            <input type="text" value=""
                                                                   name="data_campo_{$campo->getId()}"
                                                                   class="form-control form-control-sm datepicker"
                                                                   {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="col-form-label">Grupos de Signatários:</label>
                                                            <select name="grupo_assinatura_campo_{$campo->getId()}[]"
                                                                    class="ignore-validate select2 form-control grupo_assinatura"
                                                                    data-campo-id="{$campo->getId()}" multiple
                                                            >
                                                                <option value="">Selecione</option>
                                                                {if isset($grupos) and !empty($grupos)}
                                                                    {foreach $grupos as $grupo}
                                                                        <option value="{$grupo->id}"
                                                                                data-toggle="tooltip"
                                                                                {if !empty($grupo->signatarios)}
                                                                                    title="{foreach $grupo->signatarios as $signatario} - {$signatario->nome} ({$signatario->cargo})&#010; {/foreach}"
                                                                                    data-signatarios="{$grupo->signatarios}"
                                                                                {/if}
                                                                                {if !empty($grupo->empresa)}
                                                                                    data-empresa="{$grupo->empresa->id}"
                                                                                {/if}
                                                                        >{$grupo->nome}</option>
                                                                    {/foreach}
                                                                {/if}
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Signatários:</label>
                                                            <select name="signatario_assinatura_campo_{$campo->getId()}[]"
                                                                    class="select2 form-control signatario_assinatura {if $campo->getAssinaturaObrigatoria() eq false}ignore-validate{/if}"
                                                                    multiple
                                                                    {if $campo->getIsObrigatorio() eq true}required{/if}>
                                                                <option value="">Selecione</option>
                                                                {if !empty($signatarios)}
                                                                    {foreach $signatarios as $signatario}
                                                                        <option value="{$signatario->id}"
                                                                                data-toggle="tooltip"
                                                                                title="{$signatario->nome}">
                                                                            {$signatario->nome}
                                                                        </option>
                                                                    {/foreach}
                                                                {/if}
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Empresa:</label>
                                                            <select id="empresa_campo_{$campo->getId()}"
                                                                    class="select2 form-control select_empresa select_empresa_{$campo->getId()} {if $campo->getAssinaturaObrigatoria() eq false}ignore-validate{/if}"
                                                                    name="empresa_campo_{$campo->getId()}"
                                                                    {if $campo->getIsObrigatorio() eq true}required{/if}
                                                            >
                                                                <option value="">Selecione</option>
                                                                {foreach $empresas as $item}
                                                                    <option value="{$item->id}" data-toggle="tooltip"
                                                                            {if $assinatura && $assinatura->getEmpresa() === $item->id}
                                                                                selected="selected"
                                                                            {/if}
                                                                    >
                                                                        {$item->nome}
                                                                    </option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="col-form-label {if $campo->getIsObrigatorio() eq true}required{/if}">Tipo
                                                                de Documento:</label>
                                                            <select id="tipo_documento_campo_{$campo->getId()}"
                                                                    class="select2 form-control select_tipo_documento select_tipo_documento_{$campo->getId()} {if $campo->getAssinaturaObrigatoria() eq false}ignore-validate{/if}"
                                                                    name="tipo_documento_campo_{$campo->getId()}"
                                                                    {if $campo->getIsObrigatorio() eq true}required={/if}>
                                                                <option value="">Selecione</option>
                                                                {foreach $tipos_documentos as $item}
                                                                    <option value="{$item->id}" data-toggle="tooltip"
                                                                            {if $assinatura && $assinatura->getTipoDocumento() === $item->id}
                                                                                selected="selected"
                                                                            {/if}
                                                                    >
                                                                        {$item->nome}
                                                                    </option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                    </div>
                                                {/if}
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <label class="control-label {if $campo->getIsObrigatorio() eq true}required{/if}">Arquivo
                                                            (.pdf):</label>
                                                        {if $campo->getTemplate() neq null}<a
                                                            title="Ver template selecionado"
                                                            class="btn btn-link btn-sm float-right"
                                                            target="_blank"
                                                            href="{$app_url}_files/processos/templates/{$campo->getTemplate()}">
                                                                <i class="fa fa-file-word-o"></i> Download Template</a>
                                                        {/if}
                                                        <input type="file" accept="application/pdf"
                                                               id="campo_{$campo->getId()}"
                                                               name="campo_{$campo->getId()}[]" class="fileinput"
                                                               multiple="true"
                                                               {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                        <div id="campo_{$campo->getId()}-error"></div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        {elseif $campo->getTipo() eq App\Enum\TipoCampo::PROCESSO}
                                            <fieldset>
                                                <legend>
                                                    <small>
                                                        <i class="fa fa-file-text-o"></i> {$campo->getNome()}
                                                        <a href="javascript:" data-toggle="tooltip-html"
                                                           title="{$campo->getDescricao()}"><i
                                                                    class="fa fa-question-circle"></i></a>
                                                    </small>
                                                </legend>
                                                <div class="form-group">
                                                    <label class="col-f orm-label {if $campo->getIsObrigatorio() eq true}required{/if}">{$parametros['nomenclatura']}
                                                        s vinculados a este:</label>
                                                    <div class="float-right">
                                                        <a href="#" entidade="Processo"
                                                           title="Pesquisa avançada por {$parametros['nomenclatura']}"
                                                           class="btn btn-xs btn-info btn-selectionar-entidade"><i
                                                                    class="fa fa-search"></i></a>
                                                    </div>
                                                    <select class="form-control select_processo "
                                                            name="campo_{$campo->getId()}"
                                                            {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </fieldset>
                                        {else}
                                            <div class="form-group">
                                                <label class="control-label {if $campo->getIsObrigatorio() eq true}required{/if}">{$campo->getNome()}
                                                    :</label>
                                                {if $campo->getTipo() eq App\Enum\TipoCampo::TEXTO}
                                                    <input type="text" name="campo_{$campo->getId()}"
                                                           class="form-control form-control-sm {$campo->getMascara()}"
                                                           {if $campo->getIsObrigatorio() eq true}{if $campo->getIsObrigatorio() eq true}required="true"{/if}{/if}/>
                                                {elseif $campo->getTipo() eq App\Enum\TipoCampo::CAIXA_TEXTO}
                                                    <textarea name="campo_{$campo->getId()}" class="form-control"
                                                              {if $campo->getIsObrigatorio() eq true}required="true"{/if}></textarea>
                                                {elseif $campo->getTipo() eq App\Enum\TipoCampo::CAIXA_SELECAO}
                                                    <select data-placeholder="Selecione" name="campo_{$campo->getId()}"
                                                            class="form-control select2 form-control-sm"
                                                            {if $campo->getIsObrigatorio() eq true}required="true"{/if}>
                                                        <option value=""></option>
                                                        {foreach explode(";",$campo->getValoresSelecao()) as $valor}
                                                            <option value="{$valor}">{$valor}</option>
                                                        {/foreach}
                                                    </select>
                                                {elseif $campo->getTipo() eq App\Enum\TipoCampo::NUMERO}
                                                    <div class="input-group">
                                                        {if $campo->getMascara() eq App\Enum\MascaraCampo::MOEDA}
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">R$</div>
                                                            </div>
                                                        {/if}
                                                        <input type="{if $campo->getMascara() eq App\Enum\MascaraCampo::MOEDA}text{else}number{/if}"
                                                               class="form-control form-control-sm {if $campo->getMascara() eq App\Enum\MascaraCampo::MOEDA}autonumeric{/if}"
                                                               name="campo_{$campo->getId()}"
                                                               {if $campo->getIsObrigatorio() eq true}required="true"{/if}/>
                                                    </div>
                                                {elseif $campo->getTipo() eq App\Enum\TipoCampo::DATA}
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text"><i class='fa fa-calendar'></i>
                                                            </div>
                                                        </div>
                                                        <input type="text"
                                                               class="form-control form-control-sm data datepicker"
                                                               name="campo_{$campo->getId()}"
                                                               {if $campo->getIsObrigatorio() eq true}required="true"{/if}/>
                                                    </div>
                                                {elseif $campo->getTipo() eq App\Enum\TipoCampo::HORA}
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text"><i class='fa fa-clock-o'></i>
                                                            </div>
                                                        </div>
                                                        <input type="time" class="form-control form-control-sm hora"
                                                               name="campo_{$campo->getId()}"
                                                               {if $campo->getIsObrigatorio() eq true}required="true"{/if}/>
                                                    </div>
                                                {elseif $campo->getTipo() eq App\Enum\TipoCampo::EMAIL}
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text"><i
                                                                        class='fa fa-envelope-o'></i>
                                                            </div>
                                                        </div>
                                                        <input type="email" class="form-control form-control-sm"
                                                               name="campo_{$campo->getId()}"
                                                               {if $campo->getIsObrigatorio() eq true}required="true"{/if}/>
                                                    </div>
                                                {/if}
                                                <small class="form-text text-muted">{$campo->getDescricao()}
                                                </small>
                                            </div>
                                        {/if}
                                    {/if}
                                {/foreach}
                            </div>
                        </div>
                        <br/>
                    {/if}
                    {if count($setor_fase->getPerguntas()) gt 0}
                        <div class="card">
                            <div class="card-header text-center p-1"><span><i class="fa fa-question-circle-o"></i> Questionário de perguntas</span>
                            </div>
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Pergunta</th>
                                    <th class="text-center">Resposta</th>
                                    <th>Observações</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $setor_fase->getPerguntas() as $pergunta}
                                    {if $pergunta->getIsAtiva() eq true}
                                        <tr>
                                            <th class="text-center bg-light vertical-middle"
                                                style="width: 4%">{$pergunta@iteration}</th>
                                            <td>
                                                {$pergunta}
                                                <br/>
                                                <small class="text-muted">{$pergunta->getOrientacao()}</small>
                                            </td>
                                            <td class="text-center vertical-middle" style="width: 20%">
                                                <input type="hidden" value="{$pergunta->getId()}"
                                                       name="resposta_pergunta{$pergunta->getId()}"/>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-outline-success btn-sm">
                                                        <input type="radio" name="resposta_{$pergunta->getId()}"
                                                               value="1"
                                                               autocomplete="off"> <i class="fa fa-check"></i> Sim
                                                    </label>
                                                    <label class="btn btn-outline-danger btn-sm">
                                                        <input type="radio" name="resposta_{$pergunta->getId()}"
                                                               value="0"
                                                               autocomplete="off"> <i class="fa fa-times"></i> Não
                                                    </label>
                                                </div>
                                            </td>
                                            <td style="width: 30%">
                                            <textarea name="observacoes_{$pergunta->getId()}"
                                                      class="form-control"></textarea>
                                            </td>
                                        </tr>
                                    {/if}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                        <br/>
                    {/if}
                    {if count($setor_fase->getTarefas()) gt 0}
                        <div class="card">
                            <div class="card-header text-center p-1"><span><i
                                            class="fa fa-check-square-o"></i> Tarefas a serem realizadas<span
                                            style="color: red;"> *</span></span>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    {foreach $setor_fase->getTarefas() as $i=>$tarefa}
                                        {if $tarefa->getIsAtiva() eq true}
                                            <li class="list-group-item">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input check-tarefa"
                                                           id="customCheck{$tarefa->getId()}"
                                                           name="tarefa_realizada[{$i}]"
                                                           value="{$tarefa->getId()}" required>
                                                    <label class="custom-control-label"
                                                           title="{$tarefa->getOrientacao()}"
                                                           for="customCheck{$tarefa->getId()}">{$tarefa}
                                                    </label>
                                                </div>
                                            </li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            </div>
                        </div>
                        <br/>
                    {/if}
                </div>
            {/if}
        {/foreach}
    {/if}
{elseif isset($numero_fase) and $numero_fase eq 1}
    <p class="lead">*Nenhum requisito encontrado.</p>
{/if}

<script defer type="text/javascript"
        src="{$app_url}assets/js/view/tramite/requisitos.js?v={if isset($file_version) && !empty($file_version) }{$file_version}{/if}"></script>