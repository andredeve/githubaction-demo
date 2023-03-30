<nav id="menu" class="navbar navbar-expand-md navbar-dark bg-blue-gradient p-1">
    <div class="container">
        <a target="_blank" class="navbar-brand" href="{$cliente_config['site']}">
            <img width="40" height="30" class="d-inline-block align-top" src="{$app_url}assets/img/brasao-mini.png"/>
            {$cliente_config['descricao']}
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse"
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse"
                data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-center" href="#" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user-circle"></i> {$usuario_logado->getPessoa()->getPrimeiroNome()}
                    </a>
                    <div class="dropdown-menu">
                        <a title="Alterar sua senha de acesso do sistema"
                           class="dropdown-item btn-loading"
                           href="{$app_url}usuario/senha"
                        >
                            <i class="fa fa-key"></i> Alterar senha
                        </a>
                        <a title="Sair do sistema"
                           class="dropdown-item btn-loading"
                           href="{$app_url}usuario/sair"
                        >
                            <i class="fa fa-sign-out"></i> Sair
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
