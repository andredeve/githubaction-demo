<ul class="nav nav-tabs card-header-tabs">
    <li class="nav-item">
        <a class="nav-link active show" data-toggle="tab" href="#historicoTabView" role="tab" aria-controls="historicoTabView"
            aria-selected="false"><i class="fa fa-history"></i>
            Histórico geral</a>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#substituicaoTabView" role="tab" aria-controls="substituicaoTabView"
            aria-selected="false"><i class="fa fa-file"></i>
            Histórico de arquivos</a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade active show" id="historicoTabView" role="tabpanel">
        <div class="mt-3">
            {foreach $historico['historicoGeral'] as $item}
                <div class="message-item" id="m16">
                    <div class="message-inner">
                        <div class="message-head clearfix">
                            <div class="user-detail">
                                <h5 class="handle text-info">{$item['usuario']}</h5>
                                <div class="post-meta">
                                    <div class="asker-meta">
                                        <span class="qa-message-when">
                                            <span class="qa-message-when-data"><i class="fa fa-clock-o"></i>
                                                {$item['data']}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="qa-message-content">
                            <span class="text-muted">
                                {$item['mensagem']}
                                {if $item['complemento'] neq 'Registro atualizado' and $item['complemento'] neq 'Anexo cadastrado.'}
                                    {$item['complemento']}
                                {/if}
                            </span>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    <div class="tab-pane fade" id="substituicaoTabView" role="tabpanel">
        <div class="mt-3">
            {foreach $historico['historicoArquivos'] as $item}
                <div class="message-item" id="m16">
                    <div class="message-inner">
                        <div class="message-head clearfix">
                            <div class="user-detail">
                                <h5 class="handle text-info">{$item['usuario']}</h5>
                                <div class="post-meta">
                                    <div class="asker-meta">
                                        <span class="qa-message-when">
                                            <span class="qa-message-when-data"><i class="fa fa-clock-o"></i>
                                                {$item['data']}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="qa-message-content">
                            <span class="text-muted">
                                {$item['mensagem']}
                                {if $item['complemento'] neq 'Registro atualizado' and $item['complemento'] neq 'Anexo cadastrado.'}
                                    {$item['complemento']}
                                {/if}
                            </span>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>