<?php

namespace Core\Interfaces;

interface EntityInterface {

    function getId();

    public function setId(?int $id);

    function getDataCadastro();

    function setDataCadastro($data_cadastro);

    function getUltimaAlteracao();

    function setUltimaAlteracao($ultima_alteracao);
}
