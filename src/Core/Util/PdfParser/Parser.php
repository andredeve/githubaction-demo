<?php

namespace Core\Util\PdfParser;

use Core\Exception\TechnicalException;
use Core\Util\Functions;
use Exception;

require_once APP_PATH . 'lib/pdf-to-text/PdfToText.php';

/**
 * Visa tentar extrair o conteúdo de texto de um arquivo PDF. Ao efetuar a extração do texto, são efetuados duas tentativas. Caso na segunta tentativa
 * também ocorra um erro, uma Exception será disparada. Caso contrário, será retornado o conteúdo extraído ou uma string
 * em branco, caso nenhum texto tenha sido encontrado.
 */
# Foi utilizado duas bibliotecas para extração de texto, porque em determinados arquivos, umas das duas dependências falhavam.
# A primeira costuma disparar uma Exception. Enquanto a segunda, mesmo o PDF contendo texto legível, nem sempre o conteúdo
# era extraído, sendo retornado apenas uma string vazia.
class Parser {

     public function __construct()
    {
        ini_set('pcre.backtrack_limit', 10000000);
        ini_set("log_errors_max_len", 0);
    }

    /**
     * Extrair texto de arquivo PDF.
     * @throws Exception
     */
    function lxParseFile(string $filename): string
    {
        try {
            $text = $this->tryParse1($filename);
            if (!empty($text)) {
                return $text;
            }
        } catch (Exception $e) {
            $exception = new TechnicalException("A primeira tentativa de extração de texto do seguinte arquivo falhou: {$filename}.", $e);
            Functions::escreverLogErro($exception);
        }
        try {
            return $this->tryParse2($filename);
        } catch (Exception $e) {
            throw new TechnicalException("A extração de texto do seguinte arquivo falhou: {$filename}.", $e);
        }
    }

    /**
     * Primeira tentativa de extração de texto de PDF.
     * @throws Exception
     */
    private function tryParse1 (string $file_name): string {
        $document = (new \Smalot\PdfParser\Parser())->parseContent(file_get_contents($file_name));
        return $this->clear($document->getText());
    }

    /**
     * Segunda tentativa de extração de texto de PDF.
     * @throws Exception
     */
    private function tryParse2 (string $file_name): string {
        $text = (new \PdfToText($file_name))->Text;
        ob_clean();
        return $this->clear($text);
    }

    /**
     * Remove texto de marca e espaços em branco desnecessários.
     * @param string $str
     * @return string
     */
    private function clear(string $str): string {
        return trim(str_replace(" Powered by TCPDF (www.tcpdf.org)", "", $str));
    }
}