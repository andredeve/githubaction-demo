<p class="lead">{$fluxograma->getAssunto()}</p>
{*{include file="../../Tramite/Templates/fluxograma.tpl"}*}
<div id="accordionFluxograma">
    {foreach $fluxograma->getFases() as $fase}
        <div class="card">
            <div class="card-header" id="heading{$fase->getId()}">
                <h5 class="mb-0">
                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{$fase->getId()}"
                            aria-expanded="true" aria-controls="collapse{$fase->getId()}">
                        {if $fase->getNumero() eq 0}Fase Abertura {else}Fase # {$fase->getNumero()}{/if}
                    </button>
                </h5>
            </div>
            <div id="collapse{$fase->getId()}" class="collapse" aria-labelledby="heading{$fase->getId()}"
                 data-parent="#accordionFluxograma">
                <div class="card-body bg-secondary">
                    {$setores_fase=$fase->getSetoresFase()}
                    {if count($setores_fase) gt 1}
                        <div id="accordionSetorFase{$fase->getId()}">
                            {foreach $setores_fase as $setor_fase}
                                <div class="card">
                                    <div class="card-header" id="heading_setor{$setor_fase->getId()}">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link text-success" data-toggle="collapse"
                                                    data-target="#collapse_setor{$setor_fase->getId()}"
                                                    aria-expanded="true"
                                                    aria-controls="collapse_setor{$setor_fase->getId()}">
                                                <i class="fa fa-building-o"></i> {$setor_fase->getSetor()}
                                            </button>
                                        </h5>
                                    </div>
                                    <div id="collapse_setor{$setor_fase->getId()}" class="collapse"
                                         aria-labelledby="heading_setor{$setor_fase->getId()}"
                                         data-parent="#accordionSetorFase{$fase->getId()}">
                                        <div class="card-body">
                                            {include file="../../Fluxograma/Templates/requisitos.tpl"}
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    {else}
                        {$setor_fase=$setores_fase[0]}
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0 text-success">
                                    <i class="fa fa-building-o"></i> {$setor_fase->getSetor()}
                                </h6>
                            </div>
                            <div class="card-body">
                                {include file="../../Fluxograma/Templates/requisitos.tpl"}
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    {/foreach}
</div>
