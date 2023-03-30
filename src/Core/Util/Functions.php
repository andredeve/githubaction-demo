<?php

namespace Core\Util;

use App\Controller\UsuarioController;
use Core\Exception\TechnicalException;
use DateTime;
use Exception;
use Lib\TCPDF\TCPDI;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use Throwable;
use function mb_detect_encoding;
use Lib\TCPDF\TCPDF;

/**
 * Classe Funcoes
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 *
 * @copyright 2016 Lxtec Informática LTDA
 */
class Functions
{
    static function getStringBusca($busca)
    {
        $frases = array();
        if (preg_match('/"([^"]+)"/', $busca, $m)) {
            $frases[] = $m[1];
        }
        foreach ($frases as $frase) {
            $busca = str_replace('"' . $frase . '"', "", $busca);
        }
        $partes = explode(" ", $busca);
        $numero = "";
        $ano = "";
        $busca_string = "";
        foreach ($partes as $p) {
            if (!is_numeric($p)) {
                $partes_numero = explode("/", $p);
                if (count($partes_numero) > 1) {
                    $numero = $partes_numero[0];
                    $ano = $partes_numero[1];
                } else {
                    $busca_string .= !empty($p) ? (strpos($p, '-') === false ? " +" : "") . $p . "*" : "";
                }
            } else {
                $numero = $p;
            }
        }
        foreach ($frases as $frase) {
            $busca_string .= ' +"' . $frase . '"';
        }
        return array("busca_string" => $busca_string, "numero" => $numero, "ano" => $ano);
    }

    static function castToClass($class, $object)
    {
        return unserialize(preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', serialize($object)));
    }

    static function verificarExclusaoArquivo($arquivo)
    {
        if (is_file($arquivo)) {
            unlink($arquivo);
        }

    }

    static function getQntdePaginasPDF($arquivoComCaminhoCompleto){
        if(!class_exists("TCPDF")) {
            require_once(APP_PATH . '/lib/pdf-merger/tcpdf/TCPDF.php');
        }
        $orientacaoPDF = Functions::getOrientacaoPDF($arquivoComCaminhoCompleto);
        $mypdf = new TCPDI($orientacaoPDF);
        return $mypdf->setSourceFile($arquivoComCaminhoCompleto);
    }
    static function convertPtsToMm($valor){
        return $valor /2.835;
    }
    static function getTamanhoPapel($arquivoComCaminhoCompleto){
        $info = pathinfo($arquivoComCaminhoCompleto);
        if(!in_array($info["extension"], array("pdf", "PDF") ) ){
            return NULL;
        }
                 
        $infopdf = shell_exec('pdfinfo "'.$arquivoComCaminhoCompleto.'" | grep "Page.*size:"');
        $infopdf = str_replace(
                array("Page size: "," pts (A4)"), 
                "", 
                $infopdf);
        $infopdf = trim($infopdf);
        $dimencoes = explode(" x ", $infopdf);
        $dimencoes[0] = round(Functions::convertPtsToMm($dimencoes[0]));
        $dimencoes[1] = round(Functions::convertPtsToMm($dimencoes[1]));
        
        $papeis = array(
            "1189x841"  =>  "A0",
            "841x594"   =>  "A1",
            "594x420"   =>  "A2",
            "420x297"   =>  "A3",
            "297x210"   =>  "A4",
            "210x148"   =>  "A5", 
            "148x105"   =>  "A6",
            "105x74"    =>  "A7" 
        );
        return isset($papeis[$dimencoes[0]."x".$dimencoes[1]])?$papeis[$dimencoes[0]."x".$dimencoes[1]]:"A4";
    } 
    /**
     * @param $arquivoComCaminhoCompleto
     * @param $cliente
     * @param $numeracaoInicial
     * @param $assinado
     * @param $outputType string Destino para onde enviar o documento. Pode assumir um dos seguintes valores:<ul><li>I: envia o arquivo para o navegador (padrão). O plug-in é usado se disponível.</li><li>D: envia para o navegador e força o download do arquivo.</li><li>D: envia para o navegador e força o download do arquivo.</li> li><li>F: salva em um arquivo do servidor local.</li><li>S: retorna o documento como uma string.</li><li>FI: equivalente à opção F + I</li><li>FD: equivalente à opção F + D</li><li>E: retorna o documento como anexo de e-mail de várias partes mime base64 (RFC 2045)</li>< /ul>
     * @return void
     *
     */
    static  function adicionarPaginacaoECarimbo($arquivoComCaminhoCompleto, $cliente, $numeracaoInicial = 0, $assinado =false, string $outputType = 'F'){
        if (!is_file($arquivoComCaminhoCompleto)) {
            return;
        }
        $orientacaoPDF = Functions::getOrientacaoPDF($arquivoComCaminhoCompleto);
        $formatoPapel = Functions::getTamanhoPapel($arquivoComCaminhoCompleto);
        $mypdf = new TCPDI();
        $totalPages =  $mypdf->setSourceFile($arquivoComCaminhoCompleto);
        $mypdf->setPrintFooter(false);
        $mypdf->setPrintHeader(false);
        for ($i = 1; $i <= $totalPages; $i++){
            $tplIdx = $mypdf->importPage($i);
            $dimen = $mypdf->getTemplateSize($tplIdx);
            if ($dimen['w'] > $dimen['h']) {
                $mypdf->AddPage('L', [$dimen['w'], $dimen['h']]);
            } else {
                $mypdf->AddPage('P', [$dimen['w'], $dimen['h']]);
            }
            if (!$tplIdx) {
                continue;
            }
            $mypdf->useTemplate($tplIdx, 0, 0, $dimen['w'], $dimen['h'], true);
            if(isset($cliente['adicionar_carimbo']) && $cliente['adicionar_carimbo']){
                $mypdf->Image(APP_PATH . 'assets/img/carimbo.png',$cliente["margin_left_carimbo"], $cliente["margin_bottom_carimbo"],30, 30);
            }
            if(isset($cliente["adicionar_paginacao"]) && $cliente["adicionar_paginacao"]){
                $mypdf->SetFont('times');
                $mypdf->SetTextColor(0, 0, 0);
                $mypdf->SetFontSize($cliente["tamanho_paginacao"]);
                if($orientacaoPDF == "L"){
                    if($formatoPapel == "A1"){
                        $mypdf->Text($cliente["posicao_x_paginacao"]+590,$cliente["posicao_y_paginacao"],sprintf("%05s", ($numeracaoInicial+$i)));
                    }else{
                        $mypdf->Text($cliente["posicao_x_paginacao"]+90,$cliente["posicao_y_paginacao"],sprintf("%05s", ($numeracaoInicial+$i)));
                    }
                }else{
                    $mypdf->Text($cliente["posicao_x_paginacao"],$cliente["posicao_y_paginacao"],sprintf("%05s", ($numeracaoInicial+$i)));
                }
            }
        }
        $infoPDF = pathinfo($arquivoComCaminhoCompleto);
        $filename = str_replace("_original", "",$infoPDF['filename'] );
        $novoNomeArquivo = $filename. "_carimbado.". $infoPDF['extension'];
        $novoCaminhoCompleto = $infoPDF['dirname']. "/". $novoNomeArquivo;
        /*Caso seja feita a numeração de uma capa, aumenta o contador em 1
        para que os anexos do apenso sigam o fluxo de numeração corretamente
        */
        if (isset($_POST["qtdPaginas"]) && !empty($_POST["qtdPaginas"])){
            $_POST["qtdPaginas"] = $_POST["qtdPaginas"] + 1;
        }
        $mypdf->Output($novoCaminhoCompleto, $outputType);
    }

    static function getOrientacaoPDF($arquivo){
        $info = pathinfo($arquivo);
        if(!in_array($info["extension"], array("pdf", "PDF") ) ){
            return null;
        }
                 
        $infopdf = shell_exec('pdfinfo "'.$arquivo.'" | grep "Page.*size:"');
        $infopdf = str_replace(
                array("Page size: "," pts (A4)"), 
                "", 
                $infopdf);
        $infopdf = trim($infopdf);
        $dimencoes = explode(" x ", $infopdf);
        $dimencoes[0] = intval($dimencoes[0]);
        $dimencoes[1] = intval($dimencoes[1]);
        
        if($dimencoes[0] > $dimencoes[1]){
            return "L";
        }else {
            return "P";
        }    
    }

    /**
     * @throws TechnicalException
     */
    static function getDimensoesPDF($arquivo, $unit = "px"): ?array
    {
        $info = pathinfo($arquivo);
        if(!in_array($info["extension"], array("pdf", "PDF") ) ){
            return null;
        }
        $infopdf = shell_exec('pdfinfo "'.$arquivo.'" | grep "Page.*size:"');
        $infopdf = str_replace(
            array("Page size: "," pts (A4)"),
            "",
            $infopdf);
        $infopdf = trim($infopdf);
        $dimencoes = explode(" x ", $infopdf);
        if ($unit === "px") {
            $dimencoes[0] = intval($dimencoes[0]);
            $dimencoes[1] = intval($dimencoes[1]);
        } else if ($unit === "mm") {
            $dimencoes[0] = intval($dimencoes[0]) * 0.2645;
            $dimencoes[1] = intval($dimencoes[1]) * 0.2645;
        } else {
            throw new TechnicalException("Conversão para a unidade de medida \"$unit\" não disponível.");
        }
        return $dimencoes;
    }
    
    static function tirarAcentos($string)
    {
        return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
    }

    static function formatarCpfCnpj($cnpj_cpf)
    {
        $cnpj_cpf = filter_var(str_replace('/', '', str_replace('-', '', str_replace('.', "", $cnpj_cpf))), FILTER_SANITIZE_NUMBER_INT);
        if (strlen(preg_replace("/\D/", '', $cnpj_cpf)) === 11) {
            $response = preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        } else {
            $response = preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
        }

        return $response;
    }

    public static function getPrimeroParagrafoTexto($texto)
    {
        $aux = explode('.', strip_tags($texto));
        return $aux[0] . '.';
    }

    /**
     * Função que escreve em um arquivo .ini
     * @param type $assoc_arr
     * @param type $path
     * @param type $has_sections
     * @return boolean
     */
    public static function write_ini_file($assoc_arr, $path, $has_sections)
    {
        $content = '';

        if (!$handle = fopen($path, 'w'))
            return FALSE;

        self::_write_ini_file_r($content, $assoc_arr, $has_sections);

        if (!fwrite($handle, $content))
            return FALSE;

        fclose($handle);
        return TRUE;
    }

    private static function _write_ini_file_r(&$content, $assoc_arr, $has_sections)
    {
        foreach ($assoc_arr as $key => $val) {
            if (is_array($val)) {
                if ($has_sections) {
                    $content .= "[$key]\n";
                    self::_write_ini_file_r($content, $val, false);
                } else {
                    foreach ($val as $iKey => $iVal) {
                        if (is_int($iKey))
                            $content .= $key . "[] = $iVal\n";
                        else
                            $content .= $key . "[$iKey] = $iVal\n";
                    }
                }
            } else {
                $content .= "$key = $val\n";
            }
        }
    }

    /**
     * Função que verifica se valor a ser atualiza é data
     * @param string $value
     * @return boolean
     */
    public static function testDate($value)
    {
        return preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value);
    }

    /**
     * Função que verifica se valor a ser atualizado é real (monetário)
     * @param type $value
     * @return boolean
     */
    public static function testVal($value)
    {
        if (preg_match('/^(?:[1-9](?:[\d]{0,2}(?:\.[\d]{3})*|[\d]+)|0)(?:,[\d]{0,2})?$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * Função que trata String removendo os acentos, espaços
     * e transformando para minúscula
     * @param type $string
     * @return type
     */
    public static function remover_caracter($string, $separator = "-")
    {
        $string = preg_replace("/[áàâãä]/", "a", $string);
        $string = preg_replace("/[ÁÀÂÃÄ]/", "A", $string);
        $string = preg_replace("/[éèê]/", "e", $string);
        $string = preg_replace("/[ÉÈÊ]/", "E", $string);
        $string = preg_replace("/[íì]/", "i", $string);
        $string = preg_replace("/[ÍÌ]/", "I", $string);
        $string = preg_replace("/[óòôõö]/", "o", $string);
        $string = preg_replace("/[ÓÒÔÕÖ]/", "O", $string);
        $string = preg_replace("/[úùü]/", "u", $string);
        $string = preg_replace("/[ÚÙÜ]/", "U", $string);
        $string = preg_replace("/ç/", "c", $string);
        $string = preg_replace("/Ç/", "C", $string);
        $string = preg_replace("/[][><}{)(:;,!?*%~^`&#@]/", "", $string);
        $string = preg_replace("/ /", $separator, $string);
        return strtolower($string);
    }

    /**
     * Vetor constante com todos os meses e suas abreviações
     * @var type
     */
    public static $arr_meses = array(
        '1' => 'Jan',
        '2' => 'Fev',
        '3' => 'Mar',
        '4' => 'Abr',
        '5' => 'Mai',
        '6' => 'Jun',
        '7' => 'Jul',
        '8' => 'Ago',
        '9' => 'Set',
        '10' => 'Out',
        '11' => 'Nov',
        '12' => 'Dez'
    );

    /**
     * Formata número real para decimal mysql
     * @param type $valor
     * @return type
     */
    public static function realToDecimal($valor)
    {
        if (!empty($valor)) {
            $aux = str_replace(".", "", $valor);
            return str_replace(",", ".", $aux);
        }
        return 0;
    }

    /**
     * Formata número decimal mysql para exibição em real
     * @param int|float $valor
     * @return string
     */
    public static function decimalToReal($valor): string
    {
        return number_format($valor, 2, ',', '.');
    }

    public static function tratarSaidaAjax($texto)
    {
        $enc = mb_detect_encoding($texto, "UTF-8,ISO-8859-1");
        return iconv($enc, "UTF-8", $texto);
    }

    /**
     * Calcula a diferença entre dois horários em dias,horas, minutos e segundos.
     */
    public static function timerFormat($start_time, $end_time, $only_hours = false)
    {
        $total_time = $end_time - $start_time;
        if ($only_hours) {
            $horas = $total_time / 3600;
            return $horas;
        } else {
            $horas = floor($total_time / 3600);
            $minutos = intval(($total_time / 60) % 60);
            $seconds = intval($total_time % 60);
        }
        $results = "";
        /* if ($dias > 0)
          $results .= $dias . (($dias > 1) ? "d " : " d "); */
        if ($horas > 0)
            $results .= $horas . (($horas > 1) ? "h " : " h ");
        if ($minutos > 0)
            $results .= $minutos . (($minutos > 1) ? "m " : " m ");
        else if ($seconds > 0)
            $results .= $seconds . (($seconds > 1) ? "s " : " s ");
        return $results;
    }

    /**
     * Calcula a diferencas entre dois timestamp
     * @param type $start_time
     * @param type $end_time
     * @return type
     */
    public static function daysFromTime($start_time, $end_time)
    {
        $total_time = $end_time - $start_time;
        return floor($total_time / 86400);
    }

    public static function converteMes($mes)
    {
        $meses = array('Jan' => 'Jan',
            'Feb' => 'Fev',
            'Mar' => 'Mar',
            'Apr' => 'Abr',
            'May' => 'Mai',
            'Jun' => 'Jun',
            'Jul' => 'Jul',
            'Aug' => 'Ago',
            'Nov' => 'Nov',
            'Sep' => 'Set',
            'Oct' => 'Out',
            'Dec' => 'Dez');
        return $meses[$mes];
    }

    /**
     * Retorna o mês por extenso a partir do seu valor númerico(mm)
     * @param type $mes
     * @return string
     */
    public static function mesExtenso($mes)
    {
        switch ($mes) {
            case "01":
                $mes = 'Janeiro';
                break;
            case "02":
                $mes = 'Fevereiro';
                break;
            case "03":
                $mes = ' Março';
                break;
            case "04":
                $mes = ' Abril';
                break;
            case "05":
                $mes = 'Maio';
                break;
            case "06":
                $mes = 'Junho';
                break;
            case "07":
                $mes = 'Julho';
                break;
            case "08":
                $mes = 'Agosto';
                break;
            case "09":
                $mes = 'Setembro';
                break;
            case "10":
                $mes = 'Outubro';
                break;
            case "11":
                $mes = 'Novembro';
                break;
            case "12":
                $mes = 'Dezembro';
                break;
        }
        return $mes;
    }

    public static function getMeses()
    {
        $meses = array(
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro',
        );
        return $meses;
    }

    /**
     * Retorna a data atual por extenso
     * @return type
     */
    public static function dataAtual()
    {
// leitura das datas
        $dia = date('d');
        $mes = date('m');
        $ano = date('Y');
        $semana = date('w');

// configuração mes
        switch ($mes) {

            case 1:
                $mes = "Janeiro";
                break;
            case 2:
                $mes = "Fevereiro";
                break;
            case 3:
                $mes = "Março";
                break;
            case 4:
                $mes = "Abril";
                break;
            case 5:
                $mes = "Maio";
                break;
            case 6:
                $mes = "Junho";
                break;
            case 7:
                $mes = "Julho";
                break;
            case 8:
                $mes = "Agosto";
                break;
            case 9:
                $mes = "Setembro";
                break;
            case 10:
                $mes = "Outubro";
                break;
            case 11:
                $mes = "Novembro";
                break;
            case 12:
                $mes = "Dezembro";
                break;
        }


// configuração semana

        switch ($semana) {

            case 0:
                $semana = "Domingo";
                break;
            case 1:
                $semana = "Segunda-feira";
                break;
            case 2:
                $semana = "Terça-feira";
                break;
            case 3:
                $semana = "Quarta-feira";
                break;
            case 4:
                $semana = "Quinta-feira";
                break;
            case 5:
                $semana = "Sexta-feira";
                break;
            case 6:
                $semana = "Sábado";
                break;
        }
//Agora basta imprimir na tela...
        return "$semana, $dia de $mes de $ano";
    }

    /**
     * Função para gerar senhas aleatórias
     *
     * @param integer $tamanho Tamanho da senha a ser gerada
     * @param boolean $maiusculas Se terá letras maiúsculas
     * @param boolean $numeros Se terá números
     * @param boolean $simbolos Se terá símbolos
     *
     * @return string A senha gerada
     * @author    Anderson Brandão <batistoti@gmail.com>
     *
     */
    public static function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';

        $caracteres .= $lmin;
        if ($maiusculas)
            $caracteres .= $lmai;
        if ($numeros)
            $caracteres .= $num;
        if ($simbolos)
            $caracteres .= $simb;

        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand - 1];
        }
        return $retorno;
    }

    /**
     * Função que calcula a diferença em dias entre duas datas.
     * @param type $data1
     * @param type $data2
     * @return type
     */
    public static function diferencaEntreDatas($data1, $data2)
    {
        if (strstr($data1, '/')) {
            $data1 = self::converteDataParaMysql($data1);
        }
        if (strstr($data2, '/')) {
            $data2 = self::converteDataParaMysql($data2);
        }
        // Usa a função criada e pega o timestamp das duas datas:
        $time_inicial = self::geraTimestamp($data1);
        $time_final = self::geraTimestamp($data2);
        // Calcula a diferença de segundos entre as duas datas:
        $diferenca = $time_final - $time_inicial; // 19522800 segundos
        // Calcula a diferença de dias
        return $dias = (int)floor($diferenca / (60 * 60 * 24));
    }

// Cria uma função que retorna o timestamp de uma data no formato DD/MM/AAAA
    public static function geraTimestamp($data)
    {
        if (strstr($data, '/')) {
            $data = self::converteDataParaMysql($data);
        }
        return strtotime($data);
    }

    /**
     * Converte a data desejada para o padrão (dd/mm/aaaa)
     * @param string $data
     * @return false|null|DateTime
     */
    public static function converteData($data, $time = false)
    {
        if (!empty($data)) {
            $string = 'd/m/Y';
            if ($time) {
                $string .= '- H:i:s';
            }
            return Date($string, strtotime($data));
        }
        return null;
    }

    public static function gerarStringSEO($str)
    {
        $str = strtolower(utf8_decode($str));
        $i = 1;
        $str = strtr($str, utf8_decode('àáâãäåæçèéêëìíîïñòóôõöøùúûýýÿ'), 'aaaaaaaceeeeiiiinoooooouuuyyy');
        $str = preg_replace("/([^a-z0-9])/", '-', utf8_encode($str));
        while ($i > 0)
            $str = str_replace('--', '-', $str, $i);
        if (substr($str, -1) == '-')
            $str = substr($str, 0, -1);
        return $str;
    }
    public static function UT8Encode($text)
    {
        return (mb_check_encoding ( $text,  'UTF-8')) ? $text : utf8_encode($text);
    }
    /**
     * Converte a data desejada para o padrão (aaaa-mm-dd)
     */
    public static function converteDataParaMysql($data)
    {
        if (!empty($data)) {
            return date('Y-m-d', strtotime(str_replace('/', '-', $data)));
        }
        return null;
    }

    /**
     * Altera uma data para outro formato
     *
     * @param string $date String contendo a data a ser convertida.
     * @param string $format Formato da data.
     * @return DateTime Data formatada
     * @throws Exception Quando não puder converter a data
     * @author Hugo Ferreira da Silva
     */
    static function parseDate($date, $format = 'd/m/Y')
    {
        $date = trim($date);
        $dateObj = DateTime::createFromFormat($format, $date);
        if ($dateObj === false) {
            throw new TechnicalException('Data inválida:' . $date);
        }
        return $dateObj;
    }

    /**
     * Retorna a letra correspondente no alfabeto a partir de um indice
     * @param type $indice
     * @return type
     */
    public static function getLetra($indice)
    {
        $letras = array();
        foreach (range('a', 'z') as $letra) {
            $letras[] = $letra;
        }
        return $letras[$indice];
    }

    public static function getTamanhoArquivo($arquivo)
    {
        if (is_file($arquivo)) {
            $bytes = sprintf('%u', filesize($arquivo));
            if ($bytes > 0) {
                $unit = intval(log($bytes, 1024));
                $units = array('B', 'KB', 'MB', 'GB');
                if (array_key_exists($unit, $units) === true) {
                    return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
                }
            }
            return $bytes;
        }
        return 0;
    }

    static function convertBytes($value)
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }
            return $qty;
        }
    }

    /**
     * Função que busca o nome da máquina do usuário
     * @return string
     */
    public static function getMachineUserName()
    {
        return gethostbyaddr(self::getUserIp());
    }

    /**
     * Função que busca o endereço IP real do usuário
     * @return string
     */
    public static function getUserIp()
    {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = @$_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }

    static function validarCnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string)$cnpj);
        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;
        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto))
            return false;
        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj{$i} * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
    }

    /**
     * Função que calcula uma data útil a partir de uma data inicial e prazo
     * @param DateTime $data = data
     * @param int $prazo_dias
     */
    static function getDataUtil(DateTime $data, int $prazo_dias)
    {
        $vencimento = "";
        for ($i = 1; $i <= $prazo_dias; $i++) {
            $data->modify("+1 day");
            $vencimento_temp = $data->format("Y-m-d");
            $vencimento = Functions::getProxDiaUtil($vencimento_temp);
            //Se vencimento for diferente de vencimento temp, quer dizer que houve alteração de dia útil, então descontar do contador de prazo.
            if ($vencimento != $vencimento_temp) {
                $i--;
            }
        }
        return $vencimento;
    }

    /**
     * Retorna o próximo dia útil de uma data
     * @throws Exception
     */
    static function getProxDiaUtil($data)
    {
        $dia_semana = date('w', strtotime($data));
        if (in_array(strtotime($data), Functions::getDiasFeriado()) || $dia_semana == 6 || $dia_semana == 0) {
            $data = new DateTime($data);
            $data->modify("+1 day");
            return self::getProxDiaUtil($data->format('Y-m-d'));
        }
        return $data;
    }

    /**
     * Função que retorna as datas de feriados nacionais
     * @param type $ano = ano requisitado
     * @return type  array de datas
     */
    public static function getDiasFeriado($ano = null)
    {
        if ($ano === null) {
            $ano = intval(date('Y'));
        }
        $pascoa = easter_date($ano); // Limite de 1970 ou após 2037 da easter_date PHP consulta http://www.php.net/manual/pt_BR/function.easter-date.php
        $dia_pascoa = date('j', $pascoa);
        $mes_pascoa = date('n', $pascoa);
        $ano_pascoa = date('Y', $pascoa);
        $feriados = array(
            // Datas Fixas dos feriados Nacionais Basileiras
            mktime(0, 0, 0, 1, 1, $ano), // Confraternização Universal - Lei nº 662, de 06/04/49
            mktime(0, 0, 0, 4, 21, $ano), // Tiradentes - Lei nº 662, de 06/04/49
            mktime(0, 0, 0, 5, 1, $ano), // Dia do Trabalhador - Lei nº 662, de 06/04/49
            mktime(0, 0, 0, 9, 7, $ano), // Dia da Independência - Lei nº 662, de 06/04/49
            mktime(0, 0, 0, 10, 12, $ano), // N. S. Aparecida - Lei nº 6802, de 30/06/80
            mktime(0, 0, 0, 11, 2, $ano), // Todos os santos - Lei nº 662, de 06/04/49
            mktime(0, 0, 0, 11, 15, $ano), // Proclamação da republica - Lei nº 662, de 06/04/49
            mktime(0, 0, 0, 12, 25, $ano), // Natal - Lei nº 662, de 06/04/49
            mktime(0, 0, 0, 6, 13, $ano), //Santo Antônio
            // Esses feriados dependem da páscoa
            mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 48, $ano_pascoa), //2ª feira Carnaval
            mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 47, $ano_pascoa), //3ª feira Carnaval	
            mktime(0, 0, 0, $mes_pascoa, $dia_pascoa - 2, $ano_pascoa), //6ª feira Santa  
            mktime(0, 0, 0, $mes_pascoa, $dia_pascoa, $ano_pascoa), //Pascoa
            mktime(0, 0, 0, $mes_pascoa, $dia_pascoa + 60, $ano_pascoa), //Corpus Christ
        );
        sort($feriados);
        return $feriados;
    }

    static function getPorcentagem($atual, $total)
    {
        return round(($atual / $total) * 100, 2);
    }

    /**
     * Função que envia uma mensagem de resposta ao progresso de atualização
     * @param int $id = id de interação
     * @param string $message = mensagem
     * @param mixed $progress = valor do progresso
     */
    static function send_message($id, $message, $progress)
    {
        $d = array('message' => $message, 'progress' => $progress);
        echo "id: $id" . PHP_EOL;
        echo "data: " . json_encode($d) . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
        flush();
    }

    public static function sanitizeString($str)
    {
        $str = trim($str);
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
        $str = preg_replace('/[^a-z0-9]/i', '_', $str);
        $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
        return strtolower($str);
    }

    static function sanitizeNumber($str){
        return preg_replace( '/[^0-9]/', '', $str );
    }

    //------------------------------------------------------------------------------
// suporte para a manipulação de arquivos BMP
    /*     * ****************************************** */
    /* Function: ImageCreateFromBMP              */
    /* Author:   DHKold                          */
    /* Contact:  admin@dhkold.com                */
    /* Date:     The 15th of June 2005           */
    /* Version:  2.0B                            */
    /*     * ****************************************** */
    static function imagecreatefrombmp($filename)
    {
        //Ouverture du fichier en mode binaire
        if (!$f1 = fopen($filename, "rb"))
            return FALSE;
        //1 : Chargement des ent?tes FICHIER
        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
        if ($FILE['file_type'] != 19778)
            return FALSE;
        //2 : Chargement des ent?tes BMP
        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' .
            '/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
            '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
        $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0)
            $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] = 4 - (4 * $BMP['decal']);
        if ($BMP['decal'] == 4)
            $BMP['decal'] = 0;
        //3 : Chargement des couleurs de la palette
        $PALETTE = array();
        if ($BMP['colors'] < 16777216) {
            $PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
        }
        //4 : Cr?ation de l'image
        $IMG = fread($f1, $BMP['size_bitmap']);
        $VIDE = chr(0);
        $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
        $P = 0;
        $Y = $BMP['height'] - 1;
        while ($Y >= 0) {
            $X = 0;
            while ($X < $BMP['width']) {
                if ($BMP['bits_per_pixel'] == 24)
                    $COLOR = @unpack("V", substr($IMG, $P, 3) . $VIDE);
                elseif ($BMP['bits_per_pixel'] == 16) {
                    $COLOR = @unpack("n", substr($IMG, $P, 2));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 8) {
                    $COLOR = @unpack("n", $VIDE . substr($IMG, $P, 1));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 4) {
                    $COLOR = @unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 2) % 2 == 0)
                        $COLOR[1] = ($COLOR[1] >> 4);
                    else
                        $COLOR[1] = ($COLOR[1] & 0x0F);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 1) {
                    $COLOR = @unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 8) % 8 == 0)
                        $COLOR[1] = $COLOR[1] >> 7;
                    elseif (($P * 8) % 8 == 1)
                        $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                    elseif (($P * 8) % 8 == 2)
                        $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                    elseif (($P * 8) % 8 == 3)
                        $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                    elseif (($P * 8) % 8 == 4)
                        $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                    elseif (($P * 8) % 8 == 5)
                        $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                    elseif (($P * 8) % 8 == 6)
                        $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                    elseif (($P * 8) % 8 == 7)
                        $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } else
                    return FALSE;
                imagesetpixel($res, $X, $Y, $COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P += $BMP['decal'];
        }
        //Fermeture du fichier
        fclose($f1);
        return $res;
    }

    static function escreverLogErro($log)
    {
        if ($log instanceof Throwable) {
            $log = $log->getMessage() . PHP_EOL . $log->getTraceAsString();
        }
        $log_file = APP_PATH . '_log/errors.log';
        $usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
        if (is_file($log_file)) {
            $content = file_get_contents($log_file);
        } else {
            $content = "";
        }
        $nome_usuario = $usuario_logado != null ? $usuario_logado->getPessoa()->getNome() : 'não encontrado';
        $horario = Date('d/m/y - H:i:s');
        file_put_contents($log_file, $content . "\n" . "Horário: $horario | Usuário logado: " . $nome_usuario . " |  Erro: " . $log . PHP_EOL);
    }

    static function escreverLogEvento($log)
    {
        $log_file = APP_PATH . '_log/events.log';
        $usuario_logado = UsuarioController::getUsuarioLogadoDoctrine();
        if (is_file($log_file)) {
            $content = file_get_contents($log_file);
        } else {
            $content = "";
        }
        $nome_usuario = $usuario_logado != null ? $usuario_logado->getPessoa()->getNome() : 'não encontrado';
        $horario = Date('d/m/y - H:i:s');
        file_put_contents($log_file, $content . "\n" . "Horário: $horario | Usuário logado: " . $nome_usuario . " |  Mensagem: " . $log);
    }

    /**
     * @param DateTime|null $date
     * @param string $style
     * @return void
     */
    public static function formatarData($date, string $style = 'd/m/Y'): ?string
    {
        if (is_null($date)) return null;
        try {
            return date_format($date, $style);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @throws TechnicalException
     */
    public static function isPDFA($caminhoCompleto): bool
    {
        if (!is_file($caminhoCompleto)) {
            throw new TechnicalException("O arquivo não foi encontrado no caminho especificado: $caminhoCompleto");
        }
        $contentPDF = file_get_contents($caminhoCompleto);
        $subject = $contentPDF;
        $pattern = "/pdfaid:part/";
        preg_match($pattern, $subject, $matches);
        return !empty($matches) && count($matches) > 0 ;
    }

    /**
     * @throws TechnicalException
     */
    public static  function isPDAAssinado($caminhoCompleto): bool
    {
        if (!is_file($caminhoCompleto)) {
            throw new TechnicalException("O arquivo não foi encontrado no caminho especificado: $caminhoCompleto");
        }
        $contentPDF = file_get_contents($caminhoCompleto);

        $subject = $contentPDF;
        $pattern = "/adbe.pkcs7.detached/";
        preg_match($pattern, $subject, $matches);
        return !empty($matches) && count($matches) > 0 ;

    }

    public static function removerArquivo($file)
    {
        if (is_file($file)) {
            unlink($file);
        }
    }

    public static function limparCpfCnpj($valor)
    {
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        return $valor;
    }
    
    public static function isPDF($filename): bool
    {
        if (empty($filename)) {
            return false;
        };
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return strtolower($ext) === 'pdf';
    }
    
    public static function isImage($filename): bool
    {
        if (empty($filename)) {
            return false;
        }
        $mime = mime_content_type($filename);
        return str_contains($mime, "image");
    }

    /**
     */
    public static function isDocument($filename): bool
    {
        if (empty($filename)) {
            return false;
        }
        $mime = mime_content_type($filename);
        return str_contains($mime, "msword") || str_contains($mime, "document");
    }

    /**
     * @throws TechnicalException
     */
    public static function imageToPdf($filename): ?string
    {
        if (empty($filename)) {
            throw new TechnicalException("Arquivo não informado.");
        }
        $new_filename = pathinfo($filename, PATHINFO_DIRNAME) . "/" . pathinfo($filename, PATHINFO_FILENAME) . ".pdf";
        list($width, $height) = getimagesize($filename);
        $width = $width * 0.264583333337192;
        $height= $height * 0.264583333337192;
        $pdf = new TCPDF('', 'mm', [$width, $height], true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetXY(0, 0);
        $pdf->Image($filename, null, null, null, null, '', '', '', true);
        $pdf->Output($new_filename, "F");
        return $new_filename;
    }

    /**
     * @throws TechnicalException
     */
    public static function docToPdf($filename): string {
        if (empty($filename)) {
            throw new TechnicalException("Arquivo não informado.");
        }
        $info = pathinfo($filename);
        if ($info['extension'] === 'doc') {
            throw new TechnicalException("Tipo de arquivo não suportado. Media suportada: docx.");
        }
        $new_filename = $info['dirname'] . "/" . $info['filename'] . ".pdf";
        $phpWord = IOFactory::load($filename);
        $phpWord->save($new_filename, 'PDF');
        return $new_filename;
    }
}
