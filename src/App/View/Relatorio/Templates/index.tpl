{if $usuario_logado->getGrupo()->getRelatorios() eq true or $usuario_logado->getTipo() == App\Enum\TipoUsuario::MASTER}
    <div class="row">
        <div class="col-md-4">
            {foreach App\Controller\RelatorioController::getRelatorios() as $relatorio}
                <a class="btn btn-block btn-outline-info" href="{$relatorio['link']}"><i class="fa fa-file-text-o pull-left"></i> {$relatorio['descricao']}</a>
            {/foreach}
        </div>
        <div class="col text-center">
            <img height="250" src="{$app_url}assets/img/reporting.png"/>
        </div>
    </div>
{/if}
