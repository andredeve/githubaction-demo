<?php

use App\Model\Anexo;


include "../../../../bootstrap.php";
if (isset($_SESSION['anexo'])) {
    $anexo_sessao = unserialize($_SESSION['anexo']);
    $tipo = Core\Util\Functions::castToClass('\App\Model\TipoAnexo', $anexo_sessao->getTipo());
    if ($tipo->getId() != null) {
        $anexo_sessao->setTipo((new \App\Model\TipoAnexo())->buscar($tipo->getId()));
    }
    if ($anexo_sessao->getId() != null) {
        $anexo = (new Anexo())->buscar($anexo_sessao->getId());
        $imagens = $anexo_sessao->getImagens();
        foreach ($imagens as $imagem) {
            $anexo->adicionaImagem($imagem);
        }
    }
    $anexo = $anexo_sessao;
} else if (isset($_POST['anexo_id'])) {
    $anexo = (new Anexo())->buscar($_POST['anexo_id']);
} else {
    $anexo = $processo->getAnexos()->get($_POST['indice']);
}
$processo = !empty($_POST['processo_id']) ? (new \App\Model\Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
$anexo->setProcesso($processo);
if ($anexo != null) {
    if ($anexo->getImagens()->count() == 0) {
        $arquivo = $anexo->getArquivo();
        $file = $anexo->getPath() . $arquivo;
        $file_temp = \App\Model\Processo::getTempPath() . $arquivo;
        $preview_config = [];
        $preview_config[] = array(
            "caption" => $arquivo,
            "size" => is_file($file) ? filesize($file) : (is_file($file_temp) ? filesize($file_temp) : null),
            "type" => $anexo->getExtensao() == 'pdf' ? "pdf" : "image",
            "url" => "",
            "downloadUrl" => false,
            "key" => !empty($_POST['anexo_id']) ? $_POST['anexo_id'] : (isset($_POST['indice']) ? $_POST['indice'] : 0)
        );
    } else {
        $preview_config = [];
        foreach ($anexo->getImagens() as $indice => $imagem) {
            $file = $anexo->getPath() . $imagem->getArquivo();
            $file_temp = \App\Model\Processo::getTempPath() . $imagem->getArquivo();
            $preview_config[] = array(
                "caption" => "Página " . ($indice + 1),
                "size" => is_file($file) ? filesize($file) : (is_file($file_temp) ? filesize($file_temp) : null),
                "url" => "",
                "type" => "image",
                "downloadUrl" => false,
                "key" => $imagem->getId() != null ? $imagem->getId() : $indice
            );
        }
    }
}
echo json_encode(array("preview_config" => $preview_config));

