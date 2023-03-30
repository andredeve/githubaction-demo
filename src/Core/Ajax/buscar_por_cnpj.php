<?php

$cnpj = str_replace("-", "", str_replace("/", "", str_replace(".", "", $_POST['cnpj'])));
$url = "https://www.receitaws.com.br/v1/cnpj/$cnpj";
echo file_get_contents($url);

