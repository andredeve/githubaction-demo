<?php
use \App\Model\Converter;
use \App\Model\Anexo;

include 'bootstrap.php';

$_SESSION["execucao_script"] = true;
//set_time_limit(30);
$converter = new Converter();
$anexo = new Anexo();

$fp = fopen(APP_PATH. '/_log/converter.log', 'a');
fwrite($fp, "\n Data:  ".(new DateTime())->format("Y-m-d H:m:s"));

$output = "\n" . Date('d/m/Y - H:i') . " - Starting sync...";
$dirTmp = "/tmp/lx_processos/";


$conversoes = $converter->listar();

$qtdeArquivosEmConversao = 0; 
$qtdeLimiteDeConversaoAoMesmoTempo = 3;
foreach($conversoes as $converter){
    if($converter->convercaoIniciada() && !$converter->getDataTermino()){
        $qtdeArquivosEmConversao++;
    }
}
fwrite($fp, "\n Data:  qtdeArquivosEmConversao = {$qtdeArquivosEmConversao}  qtdeLimiteDeConversaoAoMesmoTempo = {$qtdeLimiteDeConversaoAoMesmoTempo} ");


$configDB = App\Controller\IndexController::getDatabaseConfig();
$dsn = 'mysql:dbname='.$configDB["db_name"].';host='.$configDB["db_host"];
$user = $configDB["db_user"];
$password =  $configDB["db_password"];
 
try{
    foreach($conversoes as $key => $converter){

        $anexo = $converter->getAnexo();
        $arquivo = $anexo->getArquivo(false,false,true);
        $aux = explode("/", $arquivo);
        $nomeArquivo = $aux[count($aux)-1];
        $fileTmp =  $dirTmp. $nomeArquivo;

        $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
        if($extensao != 'pdf') {
            fwrite($fp, "\n O ARQUIVO NÃO É PDF. Extensao: .{$extensao} "); 

            $anexo = $converter->getAnexo();        
            \App\Controller\ComponenteController::inserirComponente($anexo->getProcesso(), $anexo);

            $converter->setDataInicio((new DateTime()));
            $converter->setDataTermino((new DateTime()));
            $converter->setUltimaTamanho(-100);
            $converter->atualizar();
            continue;
        }

        $arquivoConvertido = str_replace(array(".pdf",".PDF"),"convertido_1_4.pdf", $fileTmp);
        $arquivoDestino = str_replace(array(".pdf",".PDF"),"convertido_1_4.pdf", $nomeArquivo);

        // fwrite($fp, "\nIF ".__LINE__.": ". (!$converter->convercaoIniciada() && $qtdeArquivosEmConversao < $qtdeLimiteDeConversaoAoMesmoTempo));   
        if(!$converter->convercaoIniciada() && $qtdeArquivosEmConversao < $qtdeLimiteDeConversaoAoMesmoTempo){
            if(!is_dir($dirTmp)){
                mkdir($dirTmp);
            }

            if(!file_exists($arquivo)){
                fwrite($fp, "\n\n  O ARQUIVO ANEXO NÃO EXISTE: CONVERSAO ID = {$converter->getId()} \n\n"); 
                $converter->setDataInicio((new DateTime()));
                $converter->setDataTermino((new DateTime()));
                $converter->setUltimaTamanho(-404);
                $converter->atualizar();
                continue;
            }

            fazerBackupArquivoBase64($converter, $arquivo);


            copy($arquivo, $fileTmp);
            if($fileTmp == $arquivoConvertido){
                fwrite($fp, "\n NOME DO ARQUIVO ACONVERTER É IGUAL AO NOME DA SAIDA! ");   
                die();
            }
            $hoje = date("Y-m-d H:i:s");
            fwrite($fp, "\nData: ".$hoje." CONVERSÃO ID = ".$converter->getId() ." INICIADA ");
            $converter->setDataInicio((new DateTime()));
            $converter->setUltimaTamanho(0);
            $converter->atualizar();

            $qtdeArquivosEmConversao++;
            shell_exec( "gs -dMaxBitmap=100000000 -dBufferSpace=400000000 -dDetectDuplicateImages=true -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=\"{$arquivoConvertido}\" \"{$fileTmp}\""); 
            //copy($arquivoConvertido, $arquivoDestino);
        }else if ($converter->convercaoIniciada() && !$converter->getDataTermino()){

            /*
             * SE O ARQUIVO CONVERTIDO NÃO EXISTIR COMEÇA DE NOVO
             * SE O ARQUIVO TMP NÃO EXISTIR TBM COMEÇA DE NOVO PQ PODE SER QUE ENTRE O COMEÇO DA CONVERSÃO E O FINAL 
                A MAQUINA TENHA SIDO DESLIGADA/REINICIADA E CORROMPA A CONVERSÃO
             */
            if(!file_exists($arquivoConvertido) || !file_exists($fileTmp)){
                fwrite($fp, "\n\n\n ARQUIVO NÃO ENCONTRADO RESET CONVERSÃO:  CONVERSÃO ID = ".$converter->getId() );
                $converter->setDataInicio(null);
                $converter->setUltimaTamanho(null);
                $converter->setDataInicioOCR(null);
                $converter->atualizar();
                continue;            
            }

            $size = filesize($arquivoConvertido);

            fwrite($fp, "\n TAMANHO ARQUIVO EM CONVERSÃO = ".$size ." ULTIMO TAMANHO = ". $converter->getUltimaTamanho()); 
            if($converter->getUltimaTamanho() == $size && !$converter->getDataInicioOCR() && $anexo->getTextoOCR() === null){
                $fileExistsAntes =  file_exists($anexo->getArquivo(false, false, true));
                copy($arquivoConvertido, $anexo->getArquivo(false, false, true));
                $fileExistsDepois =  file_exists($anexo->getArquivo(false, false, true));

                if($fileExistsAntes && !$fileExistsDepois){


                    $erroMensagem = "\n\n\n ##########################################################################################################" ;
                    $erroMensagem .= "\n ##########################################################################################################";
                    $erroMensagem .= "\n ##########################################################################################################";
                    $erroMensagem .= "\n ##########################################################################################################";
                    $erroMensagem .= "\n ##########################################################################################################";
                    $erroMensagem .= "\n ##########################################################################################################";
                    $erroMensagem .= "\n ##########################################################################################################";
                    $erroMensagem .= "\n ERRO TINHA O ARQUIVO FOI CORROMPIDO: CONVERSAO ID: ".$converter->getId();
                    $erroMensagem .= "\n ####################################################################################################";
                    $erroMensagem .= "\n ####################################################################################################";
                    $erroMensagem .= "\n ####################################################################################################";
                    $erroMensagem .= "\n ####################################################################################################";
                    $erroMensagem .= "\n ####################################################################################################";
                    $erroMensagem .= "\n ####################################################################################################";
                    error_log($erroMensagem);
                    fwrite($fp, $erroMensagem);
                    continue;
                }


                if(!$anexo->getTextoOCR()){
                    fwrite($fp, "\n ARQUIVO SEM OCR CONVERSÃO ID ".$converter->getId() );
                    fwrite($fp, "\n Iniciado OCR arquivo:". $anexo->getArquivo(false, false, true) );
                    $converter->setDataInicioOCR((new DateTime()));
                    $converter->atualizar();
                    $textoOCR = (new \Core\Util\PdfParser\Parser())->lxParseFile($anexo->getArquivo(false, false, true));

                    $textoOCR = str_replace(array("'",'"') , "", $textoOCR);
                    //fwrite($fp, "\n\n\n ".$textoOCR. "\n\n\n");
                    try {
                        $pdo = new PDO($dsn, $user, $password);
                    } catch (PDOException $e) {
                        echo 'Connection failed: ' . $e->getMessage();
                    }
                    $sql = "UPDATE anexo SET texto_ocr=? WHERE id=?";
                    $pdo->prepare($sql)->execute([$textoOCR, $anexo->getId()]);
                    fwrite($fp, "\n Finalizado OCR arquivo:". $anexo->getArquivo(false, false, true) );
                }

                //$converter->remover($converter->getId());
            }else if($converter->getUltimaTamanho() == $size && (($converter->getDataInicioOCR() && $anexo->getTextoOCR() !== null) || $anexo->getTextoOCR() !== null)){
                $converter->setDataTermino((new DateTime()));
                $converter->atualizar();
                $anexo = $converter->getAnexo();
                \App\Controller\ComponenteController::inserirComponente($anexo->getProcesso(), $anexo);
            }else{
                $converter->setUltimaTamanho($size);
                $converter->atualizar();
            }
        }
    }
}catch(\Exception $e){
    fwrite($fp, "\n############################### INICIO ERRO ####################\n Erro na conversão anexo id {$anexo->getId()}:\nCaminho: {$e->getTraceAsString()}\nErro: {$e->getMessage()} \n ############################# FIM ERRO ###############################" );
                    
}
fclose($fp);

function fazerBackupArquivoBase64($converter, $arquivo){
    $backupArqivoBase64 = array(
            "anexo_id" => $converter->getAnexo()->getId(),
            "converter_id" => $converter->getId(),
            "processo_id" => $converter->getAnexo()->getProcesso()->getId(),
            "arquivo_base64" => "data:application/pdf;base64,".base64_encode(file_get_contents($arquivo))
        );
}