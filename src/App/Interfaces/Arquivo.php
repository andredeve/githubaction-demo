<?php


namespace App\Interfaces;


interface Arquivo
{
    function getExtensaoArquivo();

    public function getTamanhoArquivo();

    function getPreview();

    function getArquivo($fullpath = false, $fullpath_url = false);

    static function getPath();

    static function getPathUrl();
}