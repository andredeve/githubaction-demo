<?php
require_once __DIR__ . "/../../../bootstrap.php";

echo "<pre>";
print_r((new \App\Controller\AssinaturaController())->listarGruposAssinatura());
echo "</pre>";