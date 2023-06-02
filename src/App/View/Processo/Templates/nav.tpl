<ul class="nav nav-tabs mb-2 float-left">
    <li class="nav-item">
        <a href="{$app_url}processo/enviados"
           class="nav-link  {if $selected eq 'enviados'}active{/if}"><span><i
                        class="fa fa-send-o"></i> Enviados</span>
            <span class="badge badge-warning badge-pill qtde_processos_enviados">{$qtde_enviados}</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{$app_url}processo/receber"
           class="nav-link  {if $selected eq 'receber'}active{/if}"><span><i
                        class="fa fa-envelope-open-o"></i> A Receber</span> <span
                    class="badge badge-warning badge-pill qtde_processos_receber">{$qtde_receber}</span></a>
    </li>
    <li class="nav-item">
        <a href="{$app_url}processo/abertos"
           class="nav-link  {if $selected eq 'abertos'}active{/if}"><span><i
                        class="fa fa-mail-forward"></i> Abertos</span> <span
                    class="badge badge-warning badge-pill qtde_processos_abertos">{$qtde_aberto}</span></a>

    </li>
    <li class="nav-item">
        <a href="{$app_url}processo/arquivados"
           class="nav-link  {if $selected eq 'arquivados'}active{/if}"><span><i
                        class="fa fa-archive"></i> Arquivados</span> <span
                    class="badge badge-warning badge-pill qtde_processos_arquivados">{$qtde_arquivados}</span></a>

    </li>
    {if $contribuinteHabilitado}
        <li class="nav-item">
            <a href="{$app_url}processo/contribuintes" class="nav-link  {if $selected eq 'contribuintes'}active{/if}">
                <span>
                    <i class="fa fa-users"></i> {$parametros["contribuinte"]}
                </span>
                <span class="badge badge-warning badge-pill qtde_processos_contribuintes">{$qtde_contribuintes}</span>
            </a>
        </li>
    {/if}
</ul>
<div class="float-right">
    <a title="Criar um novo processo" class="btn btn-light border btn-loading" href="{$app_url}processo/cadastrar">
        <i class="fa fa-plus"></i> Novo
    </a>
    <a title="Pesquisar por processo(s)" class="btn btn-light border btn-loading" href="{$app_url}processo/pesquisar">
        <i class="fa fa-search"></i> Pesquisar
    </a>
</div>
<div class="clearfix"></div>
