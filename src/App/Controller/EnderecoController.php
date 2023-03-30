<?php

namespace App\Controller;

use App\Model\Cidade;
use App\Model\Endereco;

/**
 * Description of EnderecoController
 *
 * @author ander
 */
class EnderecoController
{

    static function setEndereco()
    {
        $endereco_id = filter_input(INPUT_POST, 'endereco_id', FILTER_SANITIZE_NUMBER_INT);
        $endereco = new Endereco();
        if (!empty($endereco_id)) {
            $endereco = $endereco->buscar($endereco_id);
        }
        $endereco->setCep(str_replace("-", "", filter_input(INPUT_POST, 'cep')));
        $endereco->setRua(filter_input(INPUT_POST, 'rua', FILTER_SANITIZE_STRING));
        if (!empty($_POST['numero'])) {
            $endereco->setNumero(filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING));
        }
        $endereco->setBairro(filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING));
        $endereco->setComplemento(filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_STRING));
        $endereco->setCidade(!empty($_POST['cidade']) ? (new Cidade())->buscar(filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_NUMBER_INT)) : null);
        $_POST['endereco'] = $endereco;
    }

}
