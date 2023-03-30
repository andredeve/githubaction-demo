<?php
include "../../../../bootstrap.php";
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 18/12/2018
 * Time: 15:21
 */
// diretório temporário de digitalização
$base = DIGITALIZACAO_PATH;
$usuario_logado = \App\Controller\UsuarioController::getUsuarioLogadoDoctrine();
$dir = $base . $usuario_logado->getNomePastaDigitalizacao() . "/";
$tipo = \Core\Enum\TipoMensagem::ERROR;
$msg = "Nenhum arquivo encontrado";
$sleep = 20;
if (is_dir($dir)) {
        $iterator = new \FilesystemIterator($dir);
        $isDirEmpty = !$iterator->valid();
    if (!$isDirEmpty) {
        if ((time() - filemtime($iterator->key())) < $sleep) {
            $tipo = \Core\Enum\TipoMensagem::ERROR;
            $msg = "Diretório $dir não existe!";
            echo json_encode(array('tipo' => $tipo, 'msg' => $msg));
            return ""; 				
	}
        $imagens = array();
        $anexo = !empty($_POST['anexo_id']) ? (new \App\Model\Anexo())->buscar($_POST['anexo_id']) : unserialize($_SESSION['anexo']);
        $processo = !empty($_POST['processo_id']) ? (new \App\Model\Processo())->buscar($_POST['processo_id']) : unserialize($_SESSION['processo']);
        $tempDir = \App\Model\Processo::getTempPath();
        $anexo->setProcesso($processo);
        $anexo->setIsDigitalizado(true);
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && strpos($entry, '.filepart') === false) {
                    //Arquivo encontrado no diretório
                    $file = $dir . $entry;
                    $path_info = pathinfo($entry);
                    $extensao = $path_info['extension'];
                    // Novo nome do arquivo a ser salvo no servidor
                    $novo_arquivo = date("YmdHisu") . "_" . uniqid() . "_" . date("usiHdmY") . "." . $extensao;
                    //Move o arquivo para o servidor
                    $new_file = $tempDir . $novo_arquivo;
                    //var_dump($file, $new_file);
                    rename($file, $new_file);
                    chmod($new_file, '777');
                    //copy($file, $new_file);
                    //Se extensão é pdf, signfica que a digitaolização só gerou um arquivo
                    if (strtolower($extensao) == 'pdf') {
                        if (is_file($new_file)) {
                            $anexo->setArquivo($novo_arquivo);
                            $anexo->setIsOCRIniciado(true);
                            $anexo->setIsOCRFinalizado(true);
                            $conteudo_arquivo = (new \App\Util\Tesseract\TesseractOCR())->pdfToText($new_file);
                            $anexo->setTextoOCR($anexo->getTextoOCR() . $conteudo_arquivo);
                            $tipo = \Core\Enum\TipoMensagem::SUCCESS;
                            $msg = "Arquivo digitalizado encontrado.";
                        } else {
                            $tipo = \Core\Enum\TipoMensagem::ERROR;
                            $msg = "Arquivo $new_file não encontrado.";
                        }
                        break;
                    } else if (@is_array(getimagesize($file))) {
                        // Senão, a digitalização foi feita como saíde em imagens
                        $imagens[] = $novo_arquivo;
                    }
                }
            }
            closedir($handle);
            if (count($imagens) > 0) {
                $anexo->setIsOCRIniciado(false);
                $anexo->setIsOCRFinalizado(false);
                //Arquivo com lista de imagens para posterior OCR pelo servidor
                $novo_arquivo = date("YmdHisu") . "_" . uniqid() . "_" . date("usiHdmY") . ".txt";
                $txt_content = "";
                foreach ($imagens as $i => $arquivo_imagem) {
                    $imagem = new \App\Model\ImagemDigitalizada();
                    $imagem->setArquivo($arquivo_imagem);
                    $imagem->setAnexo($anexo);
                    $anexo->adicionaImagem($imagem);
                    $txt_content .= ($i > 0 ? "\n" : "") . $tempDir . $arquivo_imagem;
                }
                file_put_contents($tempDir . $novo_arquivo, $txt_content);
                $anexo->setArquivo($novo_arquivo);
                $tipo = \Core\Enum\TipoMensagem::SUCCESS;
                $msg = "Imagens lidas com sucesso.";
            }
        } else {
            $tipo = \Core\Enum\TipoMensagem::ERROR;
            $msg = "Diretório $dir inacessível!";
        }
        $_SESSION['anexo'] = serialize($anexo);
    } else {
        $tipo = \Core\Enum\TipoMensagem::ERROR;
        $msg = "Nenhum arquivo no diretório $dir";
    }
} else {
    $tipo = \Core\Enum\TipoMensagem::ERROR;
    $msg = "Diretório $dir não existe!";
}
echo json_encode(array('tipo' => $tipo, 'msg' => $msg));