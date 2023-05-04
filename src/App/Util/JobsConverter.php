<?php
include __DIR__ . '/../../../bootstrap.php';

use \App\Model\Converter;
use \App\Model\Anexo;
use Core\Exception\TechnicalException;
use Core\Util\Functions;

$_SESSION["execucao_script"] = true;
// UPDATE `converter` SET `data_iniciio_convercao`=NULL,`data_termino_convercao`=NULL,`ultimo_tamanho`=NULL,`data_inicio_ocr`=NULL WHERE 1
class JobsConverter{
    private $conexao;
    private $dirTmp; 
    
    
    private function getPDO(){
        try {
            $configDB = App\Controller\IndexController::getDatabaseConfig();
            $dsn = 'mysql:dbname='.$configDB["db_name"].';host='.$configDB["db_host"];
            $user = $configDB["db_user"];
            $password =  $configDB["db_password"];
            
            $this->conexao = new PDO($dsn, $user, $password);
            return $this->conexao;
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }  
    }
        
    public function __construct() {
        $runScript = true;
        $cont = 0;
        $limitExecucao = Core\Controller\AppController::getConversaoConfig("max_execucao_paralela");
        if($this->getQntdeConvertendo() >= $limitExecucao){
            die("Existe converssões travadas no banco de dados execute e reinicie os elementos: 
                select * from converter c where c.data_iniciio_convercao is not null 
                and c.data_termino_convercao is null ");
        }
        while($runScript ){
            
            if($cont == 0 || $cont == 30){
                echo "\n Rodando.. ";
                $cont =1;
            }else{
                echo ". ";
                $cont++;
            }
            
            $this->dirTmp = Core\Controller\AppController::getConversaoConfig("dirTmp");
            
            $conversoes = $this->listarConversoesNaoIniciadas();
            if(!empty($conversoes) && count($conversoes) > 0){
                foreach($conversoes as $converter){
                    
                    $limitExecucao = Core\Controller\AppController::getConversaoConfig("max_execucao_paralela");
                    echo "/n getQntdeConvertendo {$this->getQntdeConvertendo()}/n";
                    while($this->getQntdeConvertendo() >= $limitExecucao){
                        sleep(10);
                    }
                    $runScript = Core\Controller\AppController::getConversaoConfig("ativo");
                    $this->createThreadConverter($converter);
                }
            }
            sleep(Core\Controller\AppController::getConversaoConfig("segundos_intervalo"));
            $runScript = Core\Controller\AppController::getConversaoConfig("ativo");
        }
        
    }
    private function echoMenssagem($menssagem){
        echo "\n\n ".(new DateTime())->format('d/m/Y H:i:s')." - ".$menssagem;
    }
    public static function isPdfFile($nomeCaminho){

        /*
         * -100 - O ARQUIVO NÃO É PDF
         */
        $extensao = strtolower(pathinfo($nomeCaminho, PATHINFO_EXTENSION));
        
        if($extensao != 'pdf') {
            echo "/n {$nomeCaminho}/n";
            return false;
            
        }
        return true;
    }
    
    private function getQntdeConvertendo(){
        $sql = "SELECT c.* FROM converter c WHERE c.data_iniciio_convercao IS NOT NULL  AND c.data_termino_convercao IS NULL ";
        $sth = $this->getPDO()->query($sql);
        $conversoesEmAndamento =  $sth->fetchAll(PDO::FETCH_OBJ);
        return empty($conversoesEmAndamento)?0:count($conversoesEmAndamento);
    }
    
    private function getAssuntosProcesso($processo_id, $assunto_id){
        $query = " SELECT a.* "
                . "  FROM assunto a "
                . "  LEFT JOIN assunto_processo ap ON a.id = ap.assunto_id "
                . "  WHERE ap.processo_id = ? OR a.id = ?";
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute([$processo_id, $assunto_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);  
    }
    
    private function getProcessoByAnexo($anexo_id){
        $query = " SELECT p.* FROM processo p WHERE "
                . " EXISTS(SELECT a.id FROM anexo a WHERE a.id = ? AND p.id = a.processo_id ) ";
        
//        $sth = $this->getPDO()->query($query);
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute([$anexo_id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
        
    }
    
    private function getTipoAnexo($tipo_id){
        $query = " SELECT t.* FROM tipo_anexo t WHERE t.id = ? ";
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute([$tipo_id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
        
    private function getAnexo($anexo_id){
        $query = " SELECT a.* FROM anexo a WHERE a.id = ? ";
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute([$anexo_id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    private function getPathProcesso($anexo_id, $comNomeArquivo = false){
        $processo = $this->getProcessoByAnexo($anexo_id);
        $assuntos =  $this->getAssuntosProcesso($processo->id, $processo->assunto_id);
        $anexo = $this->getAnexo($anexo_id);
        $tipoAnexo = $this->getTipoAnexo($anexo->tipo_anexo_id);
        foreach ($assuntos as $assunto){
            $descricaoAssunto = Core\Util\Functions::sanitizeString($assunto->nome);
            
            $descricaoTipo = !empty($tipoAnexo)? Core\Util\Functions::sanitizeString($tipoAnexo->descricao). "/": '';

            if($processo->is_externo == 1){
                $path = APP_PATH."_files/processos/{$processo->exercicio}/externo/{$processo->id}/{$descricaoAssunto}/{$descricaoTipo}";
                $pathLegado = APP_PATH."_files/processos/{$processo->exercicio}/externo/{$processo->id}/{$descricaoAssunto}/fiorilli/{$descricaoTipo}";
                $pathLegadNEA = APP_PATH."_files/processos/{$processo->exercicio}/externo/{$processo->id}/{$descricaoAssunto}/nea/{$descricaoTipo}";
            }else{
                $path = APP_PATH."_files/processos/{$processo->exercicio}/{$descricaoAssunto}/{$processo->numero}/{$descricaoTipo}";
                $pathLegado = APP_PATH."_files/processos/{$processo->exercicio}/{$descricaoAssunto}/{$processo->numero}/fiorilli/{$descricaoTipo}";
                $pathLegadoNEA = APP_PATH."_files/processos/{$processo->exercicio}/{$descricaoAssunto}/{$processo->numero}/nea/{$descricaoTipo}";
            }
            $file = "{$path}{$anexo->arquivo}";
            $fileLegado = "{$pathLegado}{$anexo->arquivo}";
            $fileLegadoNEA =  "{$pathLegadoNEA}{$anexo->arquivo}";
            // $this->echoMenssagem('LINHA: '.__LINE__.'count($conversoes): '. $file);
            if (is_file($file) && file_exists($file))
            {   
                $this->echoMenssagem('LINHA: '.__LINE__.' is_file($file): '. $file);
                if($comNomeArquivo){
                    return $file;
                }
                return $path;
            }else if (is_file($fileLegado)  && file_exists($fileLegado) ){
                $this->echoMenssagem('LINHA: '.__LINE__.' is_file($fileLegado): '. $file);
                if($comNomeArquivo){
                    return $fileLegado;
                }
                return $pathLegado;
            }else if (is_file($fileLegadoNEA)  && file_exists($fileLegadoNEA) ){
                $this->echoMenssagem('LINHA: '.__LINE__.' is_file($fileLegado): '. $file);
                if($comNomeArquivo){
                    return $fileLegadoNEA;
                }
                return $pathLegadoNEA;
            }
            $this->echoMenssagem('LINHA: '.__LINE__.' nao entrou no is_file($file) nem no is_file($fileLegado) ');
            $this->echoMenssagem('LINHA: '.__LINE__.':  file -> '. $file);
            $this->echoMenssagem('LINHA: '.__LINE__.':  fileLegado -> '. $fileLegado);
        }
    }
            
    private function createThreadConverter($converter){
        
        
        echo "\n Chegou linha \n ".__LINE__;
        
        $pid = pcntl_fork();
        
        if ($pid == -1) {
            echo "Fork falied";
            die('Fork failed');
        }else if($pid){
            pcntl_wait($status); 
        }else {
            cli_set_process_title("Convertendo_{$converter->id} JobsConverter ");
            set_time_limit(800);
//            jobConverter::$jobs[getmypid()] = getmypid();
            $arquivo = $this->getPathProcesso($converter->anexo_id, true);
            $this->setInicioConversao($converter->id);
            
            
            echo "\n chegou aqui ".__LINE__;
            $arrayCaminhoDoArquivo = explode("/", $arquivo);
            $nomeArquivo = $arrayCaminhoDoArquivo[count($arrayCaminhoDoArquivo)-1];
            $fileTmp =  $this->dirTmp. $nomeArquivo;
            
            
            if(empty($arquivo)){
                /*
                 * -101 - O arquivo não existe
                 */
                echo "\n Erro: -101 - O arquivo ainda não existe ->>{$arquivo}<<- \n";
                $this->setErrorCode($converter, -101, " -101 - O arquivo ainda não existe");
                $this->resetConversao($converter, -101);
                exit(0);
                return;
            }

            echo "\n Chegou linha \n ".__LINE__;
            if(!self::isPdfFile($arquivo)){   
                /*
                 * -100 - Não é um arquivo PDF
                 */
                echo "\n Erro: -100 - Não é um arquivo PDF ->>{$arquivo}<<- \n";
                $this->setErrorCode($converter, -100, "-100 - Não é um arquivo PDF");
                exit(0);
                return;
            }
            
            $arquivoOriginal = str_replace(array(".pdf",".PDF"),"_original.pdf", $arquivo);
            if(!file_exists($arquivoOriginal)){
                copy($arquivo, $arquivoOriginal);
            }
            echo "\n Chegou linha \n ".__LINE__;
            if(!file_exists($arquivo)){
                /**
                * -404 - O ARQUIVO ANEXO NÃO EXISTE
                */
                $this->setErrorCode($converter, -404, "-404 - O ARQUIVO ANEXO NÃO EXISTE"); 
                exit(0);
            }            
            echo "\n Chegou linha \n ".__LINE__;
            if(!is_dir($this->dirTmp)){
                mkdir($this->dirTmp);
            }
            echo "\n Chegou linha \n ".__LINE__;
            copy($arquivo, $fileTmp);
            $tamanhoArquivoAntes = filesize($arquivo);
            $percentualDeSeguranca = (($tamanhoArquivoAntes)*30)/100;
            if(filesize($fileTmp) < $percentualDeSeguranca ){
                $erroMensagem = "-502 - ERRO: COPIADO PARA TMP(fileTmp) POSSIVELMENTE EM BRANCO";
                $this->setErrorCode($converter, -502,$erroMensagem);
                $this->resetConversao($converter, -502);
                error_log($erroMensagem);
                exit(0);
                return;
            }
            
            $arquivoConvertido = str_replace(array(".pdf",".PDF"),"convertido_1_4.pdf", $fileTmp);
            if($fileTmp == $arquivoConvertido){
                /**
                 * -200 - Arquivo input e output é o mesmo isso gera erro 
                 */
                error_log("\n NOME DO ARQUIVO ACONVERTER É IGUAL AO NOME DA SAIDA! ");  
                $this->setErrorCode($converter, -200,"-200 - NOME DO ARQUIVO ACONVERTER É IGUAL AO NOME DA SAIDA! ");
                exit(0);
                return;
            }
            echo "\n Chegou linha \n ".__LINE__;
            $shellResponse = ""; 
            if( Core\Controller\AppController::getConversaoConfig("pdf_a") ){
                echo "\n pdf-a \n";
                //                $shellResponse = shell_exec( "gs -dPDFA -sDEVICE=pdfwrite -dCompatibilityLevel=1.4  -sProcessColorModel=DeviceCMYK -dAutoRotatePages=/None -dFitPage -dNOPAUSE -dQUIET -dBATCH -sPDFACompatibilityPolicy=1  -sOutputFile=\"{$arquivoConvertido}\" \"{$fileTmp}\" 2>&1; echo $?"); 
                $shellResponse = shell_exec( "gs -dPDFA=2 -sDEVICE=pdfwrite -dPDFACompatibilityPolicy=1 -sProcessColorModel=DeviceCMYK  -dNOPAUSE -r600 -dBATCH  -dAutoRotatePages=/None -dQUIET -sOutputFile=\"{$arquivoConvertido}\" \"{$fileTmp}\" 2>&1; echo $?"); 
            }else{
                echo "\n nao é pdf-a \n";
                $shellResponse = shell_exec( "gs -dMaxBitmap=100000000 -dBufferSpace=400000000 -dDetectDuplicateImages=true -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=\"{$arquivoConvertido}\" \"{$fileTmp}\" 2>&1; echo $?"); 
            } 
            
            $shellResponse = trim($shellResponse);
                                    
            if(!empty($shellResponse) && $shellResponse != 0){
                echo "/n Dentro If shellResponse ".$shellResponse;
                $this->setErrorCode($converter, -700, "-700 - Erro ao executar shell_exec, mensagem: ".$shellResponse);
                exit(0);
            }else{
                echo "/n Dentro else shellResponse ".$shellResponse;
            }
            
            echo "\n Chegou linha \n ".__LINE__;
            /*
             * SE O ARQUIVO CONVERTIDO NÃO EXISTIR COMEÇA DE NOVO
             * SE O ARQUIVO TMP NÃO EXISTIR TBM COMEÇA DE NOVO PQ PODE SER QUE ENTRE O COMEÇO DA CONVERSÃO E O FINAL 
                A MAQUINA TENHA SIDO DESLIGADA/REINICIADA E CORROMPA A CONVERSÃO
             */
            if(!file_exists($arquivoConvertido) || !file_exists($fileTmp)){
                error_log("\n\n\n ARQUIVO NÃO ENCONTRADO RESET CONVERSÃO:  CONVERSÃO ID = ".$converter->getId() );
                $this->resetConversao($converter, -405);
                exit(0);
                return;
            }
//            $tamanhoArquivoAntes = filesize($arquivo);
//            $percentualDeSeguranca = (($tamanhoArquivoAntes)*30)/100;
//            if(filesize($arquivoConvertido) < $percentualDeSeguranca ){
//                $erroMensagem = "-501 - ERRO: ARQUIVO POSSIVELMENTE EM BRANCO";
//                $this->setErrorCode($converter, -501,$erroMensagem);
//                $this->resetConversao($converter, -501);
//                error_log($erroMensagem);
//                exit(0);
//                return;
//            }
            
            $fileExistsAntes =  file_exists($arquivo);
            copy($arquivoConvertido, $arquivo);
            $fileExistsDepois =  file_exists($arquivo);

            if($fileExistsAntes && !$fileExistsDepois){
                $this->setErrorCode($converter, -500, "-500 - ERRO: TINHA O ARQUIVO MAS ESTE FOI CORROMPIDO");
                $erroMensagem = "\n\n\n ##########################################################################################################" ;
                $erroMensagem .= "\n ##########################################################################################################";
                $erroMensagem .= "\n ##########################################################################################################";
                $erroMensagem .= "\n ##########################################################################################################";
                $erroMensagem .= "\n ##########################################################################################################";
                $erroMensagem .= "\n ##########################################################################################################";
                $erroMensagem .= "\n ##########################################################################################################";
                $erroMensagem .= "\n ERRO: TINHA O ARQUIVO MAS ESTE FOI CORROMPIDO: CONVERSAO ID: ".$converter->getId();
                $erroMensagem .= "\n ####################################################################################################";
                $erroMensagem .= "\n ####################################################################################################";
                $erroMensagem .= "\n ####################################################################################################";
                $erroMensagem .= "\n ####################################################################################################";
                $erroMensagem .= "\n ####################################################################################################";
                $erroMensagem .= "\n ####################################################################################################";
                error_log($erroMensagem);
                exit(0);
                return;
            }
            
            $anexo = $this->getAnexo($converter->anexo_id);
            if(!$anexo->texto_ocr){
                
                // $this->setInicioOCRConversao($converter->id);

                // try {
                //     if (!Functions::isPDFA($arquivoOriginal)) {
                //         echo "\n Chegou linha \n " . __LINE__;
                //         $textoOCR = (new \Core\Util\PdfParser\Parser())->lxParseFile($arquivoOriginal);
                //         echo "\n Chegou linha \n " . __LINE__;
                //         $textoOCR = str_replace(array("'", '"'), "", $textoOCR);
                //         echo "\n Chegou linha \n " . __LINE__;
                //         $this->setTextoOCR($textoOCR, $anexo->id);
                //     }
                // } catch (TechnicalException $e) {
                //     Functions::escreverLogErro($e);
                // } catch (\Exception $e) {
                //     $this->setErrorCode($converter, -600, "-600 Erro ao fazer OCR. ");
                //     Functions::escreverLogErro($e);
                //     exit(0);
                // }

                echo "\n Chegou linha \n ".__LINE__;
                
            }
            echo "\n Chegou linha \n ".__LINE__;
            
            
            $this->setTerminoConversao($converter->id);
            
            echo "\nIniciando envio para assinatura";
            $converterAux = (new Converter())->buscar($converter->id);
            $anexo = $converterAux->getAnexo();
            $assinatura = new \App\Model\Assinatura();
            $assinatura = \App\Model\Assinatura::buscarPorAnexo($anexo);
            echo "\n Chegou linha \n ".__LINE__;
            if($assinatura && $assinatura->getPreenvio()){
                (new App\Controller\AssinaturaController())->concluirEnvioParaAssinatura($assinatura);
                echo "\n Envio da assinatura concluida \n ".__LINE__;
            }
            echo "\n Excluindo Arquivo do TMP \n";
            unlink($fileTmp);
            unlink($arquivoConvertido);      
            echo "\n FIM DE JOGO \n ".__LINE__;
            exit(0);
        }
        
        return;
                
    }
    
    private function listarConversoesNaoIniciadas(){
        $sth = $this->getPDO()->query('SELECT * FROM `converter` WHERE data_iniciio_convercao IS NULL ');
        return $sth->fetchAll(PDO::FETCH_OBJ);        
    }
    
    private function resetConversao($converter, $codigoErro){
        $sql = "UPDATE converter "
                . "SET data_iniciio_convercao=?, data_termino_convercao=?, "
                . "     ultimo_tamanho=?, data_inicio_ocr=?  WHERE id=?";
        $params = array(
            null,
            null,
            $codigoErro,
            null,
            $converter->id
        );
        $this->getPDO()->prepare($sql)->execute($params);   
    }
    
    private function concluirEnvioAssinatura($assinatura_id, $lxsign_id){
        $sql = "UPDATE assinatura "
                . " SET lxsign_id=?, preenvio=0 "
                . " WHERE id=? ";
        $params = array(
            $lxsign_id,
            $assinatura_id
        );
        $this->getPDO()->prepare($sql)->execute($params);   
    }
    
    private function setErrorCode($converter, $codigoErro, $observacaoErro){
        $sql = "UPDATE converter "
                . "SET data_iniciio_convercao=?, data_termino_convercao=?, "
                . "     ultimo_tamanho=?, observacao=?  WHERE id=?";
        $params = array(
            (new DateTime())->format("Y-m-d H:i:s"),
            (new DateTime())->format("Y-m-d H:i:s"),
            $codigoErro,
            $observacaoErro,
            $converter->id
        );
        $this->getPDO()->prepare($sql)->execute($params);    
    }
    
    private function setInicioConversao($converter_id){
        $configDB = App\Controller\IndexController::getDatabaseConfig();
        $dsn = 'mysql:dbname='.$configDB["db_name"].';host='.$configDB["db_host"];
        $user = $configDB["db_user"];
        $password =  $configDB["db_password"];
        $pdo = new PDO($dsn, $user, $password);
        $sql = "UPDATE converter SET data_iniciio_convercao=? WHERE id=?";
        $pdo->prepare($sql)->execute([(new DateTime())->format("Y-m-d H:i:s"), $converter_id]);
    } 
    
    private function setTerminoConversao($converter_id){
        $configDB = App\Controller\IndexController::getDatabaseConfig();
        $dsn = 'mysql:dbname='.$configDB["db_name"].';host='.$configDB["db_host"];
        $user = $configDB["db_user"];
        $password =  $configDB["db_password"];
        $pdo = new PDO($dsn, $user, $password);
        $sql = "UPDATE converter SET data_termino_convercao=? WHERE id=?";
        
        echo "\n query UPDATE converter SET data_termino_convercao=? WHERE id=? \n"
        . "date: ".(new DateTime())->format("Y-m-d H:i:s"). " converter_id: ".$converter_id;
        $pdo->prepare($sql)->execute([(new DateTime())->format("Y-m-d H:i:s"), $converter_id]);
    } 
    
    private function setInicioOCRConversao($converter_id){
        $configDB = App\Controller\IndexController::getDatabaseConfig();
        $dsn = 'mysql:dbname='.$configDB["db_name"].';host='.$configDB["db_host"];
        $user = $configDB["db_user"];
        $password =  $configDB["db_password"];
        $pdo = new PDO($dsn, $user, $password);
        $sql = "UPDATE converter SET data_inicio_ocr=? WHERE id=?";
        $pdo->prepare($sql)->execute([(new DateTime())->format("Y-m-d H:i:s"), $converter_id]);
    } 
    
    private function setTextoOCR($textoOCR, $anexo_id){
        try {
            $configDB = App\Controller\IndexController::getDatabaseConfig();
            $dsn = 'mysql:dbname='.$configDB["db_name"].';host='.$configDB["db_host"];
            $user = $configDB["db_user"];
            $password =  $configDB["db_password"];
            $pdo = new PDO($dsn, $user, $password);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        $sql = "UPDATE anexo SET texto_ocr=? WHERE id=?";
        $pdo->prepare($sql)->execute([$textoOCR, $anexo_id]);
    }
}

$jobConverter = new JobsConverter();