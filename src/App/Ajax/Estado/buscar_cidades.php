<?php

use App\Model\Estado;

include '../../../../bootstrap.php';

if (!empty($_POST['uf'])) {
    $estado = new Estado();
    $estado = $estado->buscarPorCampos(array('uf' => $_POST['uf']));
    $options = "<option value=''>Selecione</option>";
    foreach ($estado->getCidades() as $cidade) {
        $options .= "<option value='" . $cidade->getId() . "'>" . $cidade->getNome() . "</option>";
    }
    echo $options;
}
