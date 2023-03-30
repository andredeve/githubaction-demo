{function mostraFases}
    {foreach $assunto->getFluxograma()->getFases() as $indice_f=>$fase}
        {if $fase->getAtivo() eq false}
            {continue}
        {/if}
        {if $fase->getNumero() eq $numero_fase}
            {$btn_class="btn-warning"}
            {$icon="fa-send-o"}
        {elseif $fase->getNumero() eq $processo->getNumerofase()}
            {$btn_class="btn-primary"}
            {$icon="fa-file-text-o"}
        {elseif  $fase->getNumero() lt $processo->getNumeroFase()}
            {$btn_class="btn-light border"}
            {$icon="fa-check"}
        {else}
            {$btn_class="btn-light"}
            {$icon=""}
        {/if}
        <div class="stepwizard-step">
            <a href="#step-{$fase->getNumero()}" type="button" class="btn {$btn_class} btn-circle"
               disabled="disabled"><i class="fa {$icon}"></i></a>
            <p>
                Fase {$fase->getNumero()}
                {if $fase->getNumero() eq $processo->getNumerofase() && $processo->getAssuntos()->count() eq 0}
                    {$setores_atual=$processo->getSetorAtual(false)}
                    {if is_array($setores_atual)}
                        {foreach $setores_atual as $setor_atual}
                            <br/>
                            <small style="cursor: help"
                                   title="{$setor_atual->getNome()}">{$setor_atual->getNome()}
                            </small>
                        {/foreach}
                    {else}
                        <br/>
                        <small style="cursor: help"
                               title="{$setores_atual->getNome()}">{$setores_atual->getNome()}
                        </small>
                    {/if}
                {else}
                    {foreach $fase->getSetoresFase() as $setorFase}
                        <br/>
                        <small style="cursor: help"
                               title="{$setorFase->getSetor()->getNome()}">{$setorFase->getSetor()->getNome()}
                        </small>
                    {/foreach}
                {/if}
            </p>
        </div>
    {/foreach}
{/function}
<p class="lead text-center">Fluxograma: {$assunto}</p>
<div class="stepwizard">
    <div class="stepwizard-row setup-panel">
        <div class="stepwizard-step">
            <a href="#step-1" type="button" class="btn btn-success btn-circle"><i
                        class="fa fa-folder-open-o"></i></a>
            <p>Início</p>
        </div>
        {*{if $processo->getAssuntos()->count() gt 0}
            {foreach $processo->getAssuntos() as $assuntoProcesso}
                {mostraFases assunto=$assuntoProcesso->getAssunto()}
            {/foreach}
        {/if}*}
        {mostraFases assunto=$assunto}
        <div class="stepwizard-step">
            <a href="#step-3" type="button" class="btn btn-danger btn-circle" disabled="disabled"><i
                        class="fa fa-folder-o"></i></a>
            <p>Fim</p>
        </div>
    </div>
</div>
