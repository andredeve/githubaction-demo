<?php
namespace App\Util;

use Core\Util\Functions;
use Core\Util\PdfParser\Parser;

class OCR extends \Threaded implements \Collectable {
	private $garbage = false;
	
        private $anexo;
        private $caminhoCompletoArquivo; 
        
        public function __construct($anexo, $dir_arquivo , $nome_arquivo) {
            $this->anexo = $anexo;
            $this->caminhoCompletoArquivo = $dir_arquivo.$nome_arquivo;
            
        }


        public function run() {
            echo "Hello World\n";
            try {
                $this->anexo->setTextoOCR((new Parser())->lxParseFile($this->caminhoCompletoArquivo));
            } catch (\Exception $e) {
                Functions::escreverLogErro($e);
            }

            ob_start();
            echo __FILE__ . ' LINHA: ' . __LINE__;
            echo '<pre>';
            var_dump($this->anexo->getTextoOCR());
            echo '</pre>';
            $print_log = ob_get_contents();
            ob_clean();
            error_log($print_log);
            
//            $this->garbage = true;
	}
	
	public function isGarbage(): bool {
		return $this->garbage;
	}
}