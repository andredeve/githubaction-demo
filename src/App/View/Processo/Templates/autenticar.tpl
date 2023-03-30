<div class="card bg-danger text-white">
    {if $modal eq true}
        <div class="card-header">
            <h4 class="text-center"><i class="{$page_icon}"></i> {$page_title} </h4>
        </div>
    {/if}
    <div class="card-body bg-warning text-danger">
        <form method="POST" id="loginProcessoForm" class="form-signin" action="{$app_url}usuario/autenticarProcesso">
            <input type="hidden" id="app_url" name="processo_id" value="{$processo->getId()}"/>
            <h3 class="h3 mb-3 font-weight-normal text-center">Autenticação necessária</h3>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-user"></i></div></div>
                    <input type="text" name="login" class="form-control form-control-lg" autofocus="true" placeholder="Usuário" required="true"/>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-lock"></i></div></div>
                    <input type="password" name="senha" class="form-control form-control-lg" placeholder="Senha" required="true"/>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-info btn-block btn-lg ladda-button" data-style="zoom-in"><i class="fa fa-unlock"></i> Visualizar Processo</button>
            </div>
            <div class="alert alert-warning">
                O processo <strong>{$processo}</strong> é <u>sigiloso</u>. Por questões de segurança, informe seu login e senha novamente para visualizá-lo.
            </div>
        </form>
    </div>
</div>
