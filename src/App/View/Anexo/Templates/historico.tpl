{foreach $historico as $item}
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