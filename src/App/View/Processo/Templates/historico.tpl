<ul class="timeline">
    {foreach $processo->getHistorico() as $historico}
        <li>
            <div class="timeline-badge info">
                {if $historico->getTipo() eq \App\Enum\TipoHistoricoProcesso::CRIADO}
                    <i class="fa fa-plus-square-o"></i>
                {elseif $historico->getTipo() eq \App\Enum\TipoHistoricoProcesso::ATUALIZADO}
                    <i class="fa fa-save"></i>
                {elseif $historico->getTipo() eq \App\Enum\TipoHistoricoProcesso::VISUALIZADO}
                    <i class="fa fa-eye"></i>
                {elseif $historico->getTipo() eq \App\Enum\TipoHistoricoProcesso::ENVIADO}
                    <i class="fa fa-send-o"></i>
                {elseif $historico->getTipo() eq \App\Enum\TipoHistoricoProcesso::ARQUIVADO}
                    <i class="fa fa-folder-open-o"></i>
                {elseif $historico->getTipo() eq \App\Enum\TipoHistoricoProcesso::NOVO_ANEXO}
                    <i class="fa fa-paperclip"></i>
                {elseif $historico->getTipo() eq \App\Enum\TipoHistoricoProcesso::CANCELADO_ENVIO}
                    <i class="fa fa-times-rectangle-o"></i>
                {elseif $historico->getTipo() eq \App\Enum\TipoHistoricoProcesso::RECEBIDO}
                    <i class="fa fa-check"></i>
                {/if}
            </div>
            <div class="timeline-panel">
                <div class="timeline-heading">
                    <h6 class="timeline-title"><i class="fa fa-user-circle-o"></i> {$historico->getUsuario()}</h6>
                    {if $historico->getIp() neq null and $historico->getMaquina() neq null}
                        <span><i class="fa fa-desktop"></i> {$historico->getMaquina()} (I.P.:{$historico->getIp()})</span>
                    {/if}
                    <p>
                        <small class="text-muted"><i
                                    class="fa fa-clock-o"></i> {$historico->getHorario()->format('d/m/Y')}
                            às {$historico->getHorario()->format('H:i')}</small>
                    </p>
                </div>
                <div class="timeline-body">
                    <p>{$historico->getMensagem()}</p>
                </div>
            </div>
        </li>
    {/foreach}
</ul>
