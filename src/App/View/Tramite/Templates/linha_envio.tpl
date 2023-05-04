<tr id="linha_{if $setor neq null}{$setor->getId()}{/if}" class="linha-destino">
    {if $i eq 0 && isset($setor_origem) and $setor_origem neq null}
        <td id="linha_setor_origem" rowspan="{$qtde_linhas*2}" class="vertical-middle">{$setor_origem->getNome()}</td>
    {/if}
    <td rowspan="2" class="vertical-middle">
        {if $setor neq null and $numero_fase gt 1}
            <select setor_id="{$setor->getId()}" id="select_setor_destino" name="setor_destino_id[{$i}]"
                    class="select2Tree" required="true">
                <option class="10" value="">Selecione o Setor</option>
                {if isset($setores) and count($setores) > 0}
                    {include file="../../Setor/Templates/select.tpl" setores=$setores setor_selecionado=array($setor->getId())}
                {else}
                    {include file="../../Setor/Templates/select.tpl" setor_selecionado=array($setor->getId())}
                {/if}
            </select>
            <div class="custom-control custom-checkbox mt-2">
                <input type="checkbox" class="custom-control-input" name="devolverSetorOrigem"
                       id="devolverSetorOrigemCheck">
                <label class="custom-control-label" for="devolverSetorOrigemCheck">Devolver para o setor
                    anterior</label>
            </div>
        {elseif $setor eq null}
            <div class="float-right">
                
                <a id="cadastrar:Setor"
                   href="#" href_select="select_setor_destino"
                   title="Cadastrar novo Setor"
                   class="btn btn-xs btn-success btn-cadastrar-modal"><i
                            class="fa fa-plus"></i></a>
            </div>
            <br><br>
            <select id="select_setor_destino" name="setor_destino_id[{$i}]" class="select2Tree" required="true">
                <option class="10" value="">Selecione o Setor</option>
                {include file="../../Setor/Templates/select.tpl"}
            </select>
            {if !isset($primeiro_tramite)}
                <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox" class="custom-control-input" name="devolverSetorOrigem"
                           id="devolverSetorOrigemCheck">
                    <label class="custom-control-label" for="devolverSetorOrigemCheck">Devolver setor de origem</label>
                </div>
            {/if}
        {else}
            <input type="hidden" name="setor_destino_id[{$i}]" value="{$setor->getId()}">
            {$setor->getNome()}
        {/if}
    </td>
    <td style="width: 15%">
        <select name="status_processo_id[{$i}]" class="form-control form-control-sm status-processo" required="true">
            <option value="">Selecione</option>
            {foreach $status_processo as $status}
                <option value="{$status->getId()}"
                        {if isset($status_inicial_id)}{if $status_inicial_id eq $status->getId()}selected{/if}{/if}>{$status->getDescricao()}</option>
            {/foreach}
        </select>
    </td>
    <td style="width: 20%">            
        {if $processo->bloquearTramiteParaUsuario()}
            <div class="col-12 text-center" ><strong> BLOQUEADO </strong></div>
        {else}    
            <select name="usuario_destino_id[{$i}]" class="form-control form-control-sm usuario_destino_processo"
                    {if \App\Enum\SigiloProcesso::RESTRICAO_PUBLICA !=  $processo->getSigilo() && \App\Enum\SigiloProcesso::SEM_RESTRICAO !=  $processo->getSigilo() }  required="true"  {/if} >
                <option value="">Todos </option>
                {if $setor neq null}
                    {foreach $setor->getUsuarios() as $usuario}
                        <option value="{$usuario->getId()}">{$usuario->getPessoa()->getNome()}</option>
                    {/foreach}
                {/if}
            </select>
            {if \App\Enum\SigiloProcesso::RESTRICAO_PUBLICA !=  $processo->getSigilo() && \App\Enum\SigiloProcesso::SEM_RESTRICAO !=  $processo->getSigilo() }
                <span class="text-danger"> O campo é obrigatório por que esse processo é {strtolower(\App\Enum\SigiloProcesso::getOptions($processo->getSigilo()))}.   </span>
            {/if}
        {/if}
    </td>
    <td style="width: 18%">
        <input type="text" autocomplete="off" name="prazo_destino[{$i}]"
               value="{\Core\Util\Functions::converteData($prazo_destino)}"
               class="form-control form-control-sm datepicker"/>
    </td>
</tr>
<tr id="sublinha_{if $setor neq null}{$setor->getId()}{/if}">
    <td colspan="3">
        <textarea placeholder="Parecer/Observações" name="descricao_tramite[{$i}]"
                  class="form-control parecer_processo"></textarea>
    </td>
</tr>
