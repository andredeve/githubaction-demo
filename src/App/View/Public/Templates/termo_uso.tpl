<div class="block">
    <div class="centered m-0">
        <div class="card border-0 m-2">
            <h6 class="card-title">
                {str_replace(array("<contribuinte>","<app_name>"),array($contribuinte, $app_config['app_name']), $termos['cabecalho'])}
            </h6>

            <p class="card-text">
                {str_replace(array("<contribuinte>","<app_name>", "<cliente>", "<estado>"),array($contribuinte, $app_config['app_name'], $cliente['descricao'], $cliente['estado']), $termos['conteudo'])}
                <a href="{$app_url}contribuinte/signup">Clique aqui para continuar</a>
            </p>
        </div>
        <p class="text-center footer invisible">
            <small> © 2017. Desenvolvido pela <em><a title="Visitar site" target="_blank"
                        href="{$app_config['author_link']}">{$app_config['app_author']}</a></em>.
            </small>
        </p>
    </div>
</div>