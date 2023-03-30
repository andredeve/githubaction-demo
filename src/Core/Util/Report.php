<?php
namespace Core\Util;

use App\Controller\IndexController;
use Core\Controller\AppController;
use Lib\TCPDF\TCPDI;
use const APP_PATH;

//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000")
{
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R'] = $rouge;
    $tbl_couleur['V'] = $vert;
    $tbl_couleur['B'] = $bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimeter at 72 dpi
function px2mm($px)
{
    return $px * 25.4 / 72;
}

function txtentities($html)
{
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}

////////////////////////////////////

/**
 * Classe Report
 * @version 1.0
 * @author Anderson Brandão <batistoti@gmail.com>
 *
 * @copyright 2016 Lxtec Informática LTDA
 */
class Report extends TCPDI
{

    protected $title;
    //variables of html parser
    protected $B;
    protected $I;
    protected $U;
    protected $HREF;
    protected $fontList;
    protected $issetfont;
    protected $issetcolor;

    function __construct($title, $orientation = 'P')
    {
        $this->title = $title;
        $this->SetTitle($title, true);
        $this->parsers = [];
//        $this->charset_in = 'windows-1252';
        parent::__construct($orientation);
        $this->B = 0;
        $this->I = 0;
        $this->U = 0;
        $this->HREF = '';
        $this->fontlist = array('arial', 'times', 'courier', 'helvetica', 'symbol');
        $this->issetfont = false;
        $this->issetcolor = false;
    }

    public function createRowPdf($fpdf, $name, $text, $name_w = 40, $font_size = 11, $line_height = 7, $fill = true)
    {
        $fpdf->SetFont("times", "B", $font_size);
        $fpdf->SetFillColor(224, 235, 255);
        $fpdf->Cell($name_w, $line_height, $name, 1, 0, 'L', $fill);
        $fpdf->SetFont("times", "", $font_size);
        $fpdf->Cell(0, $line_height, $text, 1, 1, 'L');
    }

    /**
     * Método que constroi um cabeçalho de um tabela
      */
    function setTableHeader($headers = null, $line_heigh = 5, $border = 1, $fill = false)
    {
        if (!is_null($headers)) {
            $tam = count($headers);
            for ($i = 0; $i < $tam; $i++) {
                $line_break = $i == ($tam - 1) ? 1 : 0;
                $this->Cell($this->widths[$i], $line_heigh, $headers[$i], $border, $line_break, $this->aligns[$i], $fill);
            }
        }
    }

    protected function getCliente()
    {
        //return (new Empresa())->buscar(UsuarioController::getEmpresaLogada()->getId());
    }

    /**
     * Define um cabeçalho para todos os relatórios
     */
    function Header()
    {
        $cliente = IndexController::getClienteConfig();
        $this->Image(APP_PATH . 'assets/img/brasao.png', 10, 10, 23, 18);
        $this->SetFont('times', 'B', 14);
        $this->Cell(30);
        $this->Cell(70, 5, $cliente['nome'], 0, 2, 'L');
        $this->SetFont('Helvetica', '', 12);
        $this->SetFont('Helvetica', '', 8);
        $this->Cell(70, 4, $cliente['endereco'], 0, 2, 'L');
        $this->Cell(70, 3, 'CNPJ: ' . $cliente['cnpj'] . " / Telefone: " . $cliente['telefone'], 0, 2, 'L');
        $this->SetFont('times', 'B', 10);
        $this->Cell(70, 10, $this->title, 0, 0, 'L');
        $this->SetFont('Helvetica', '', 10);
        $this->Cell(0, 5, Date('d/m/Y'), 0, 2, "R");
        $this->Ln(2);
        $this->SetDrawColor(211, 211, 211);
        $this->Cell(0, 1, '', 'B', 2);
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetDrawColor(211, 211, 211);
// Position at 1.5 cm from bottom
        $this->SetY(-15, false);
        // Arial italic 8
        $this->SetFont('times', '', 8);
        // Informações do Sistema
        $config = AppController::getConfig();
        $this->Cell(0, 10, $config['app_name'] . ' v.' . $config['app_version'] . " - " . $config['app_description'], 'T', 0, 'L');
// Page number
        $this->Cell(0, 10, $this->PageNo() . "/" . $this->getAliasNbPages(), 'T', 0, 'R');
    }

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function WriteHTML($html, $ln = true, $fill = false, $reseth = false, $cell = false, $align = '') {
        //HTML parser
        $html = strip_tags($html, "<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
        $html = str_replace("\n", ' ', $html); //remplace retour à la ligne par un espace
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                //Text
                if ($this->HREF)
                    $this->PutLink($this->HREF, $e);
                else
                    $this->Write(5, stripslashes(txtentities($e)));
            } else {
                //Tag
                if ($e[0] == '/')
                    $this->CloseTag(strtoupper(substr($e, 1)));
                else {
                    //Extract attributes
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
        $this->Ln();
    }
//    function WriteHTML($html, $mode = HTMLParserMode::DEFAULT_MODE, $init = true, $close = true)
//    {
//        //HTML parser
//        $html = strip_tags($html, "<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
//        $html = str_replace("\n", ' ', $html); //remplace retour à la ligne par un espace
//        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
//        foreach ($a as $i => $e) {
//            if ($i % 2 == 0) {
//                //Text
//                if ($this->HREF)
//                    $this->PutLink($this->HREF, $e);
//                else
//                    $this->Write(5, stripslashes(txtentities($e)));
//            } else {
//                //Tag
//                if ($e[0] == '/')
//                    $this->CloseTag(strtoupper(substr($e, 1)));
//                else {
//                    //Extract attributes
//                    $a2 = explode(' ', $e);
//                    $tag = strtoupper(array_shift($a2));
//                    $attr = array();
//                    foreach ($a2 as $v) {
//                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3))
//                            $attr[strtoupper($a3[1])] = $a3[2];
//                    }
//                    $this->OpenTag($tag, $attr);
//                }
//            }
//        }
//        $this->Ln();
//    }

    function OpenTag($tag, $attr)
    {
        //Opening tag
        switch ($tag) {
            case 'STRONG':
                $this->SetStyle('B', true);
                break;
            case 'EM':
                $this->SetStyle('I', true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag, true);
                break;
            case 'A':
                $this->HREF = $attr['HREF'];
                break;
            case 'IMG':
                if (isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if (!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if (!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                }
                break;
            case 'TR':
            case 'BLOCKQUOTE':
            case 'BR':
                $this->Ln(5);
                break;
            case 'P':
                $this->Ln(10);
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR'] != '') {
                    $coul = hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'], $coul['V'], $coul['B']);
                    $this->issetcolor = true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont = true;
                }
                break;
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if ($tag == 'STRONG')
            $tag = 'B';
        if ($tag == 'EM')
            $tag = 'I';
        if ($tag == 'B' || $tag == 'I' || $tag == 'U')
            $this->SetStyle($tag, false);
        if ($tag == 'A')
            $this->HREF = '';
        if ($tag == 'FONT') {
            if ($this->issetcolor == true) {
                $this->SetTextColor(0);
            }
            if ($this->issetfont) {
                $this->SetFont('times');
                $this->issetfont = false;
            }
        }
    }

    function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (array('B', 'I', 'U') as $s) {
            if ($this->$s > 0)
                $style .= $s;
        }
        $this->SetFont('', $style);
    }

    function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }

    function checkPageBreak($h = 0, $y = '', $addpage = true)
    {
        $result = parent::checkPageBreak($h, $y, $addpage);
        if ($result && $this->print_header) {
            $this->SetY($this->GetY() + 25);
        }
        return $result;
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->clMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    function SetDash($black = false, $white = false)
    {
        if ($black and $white) {
            $s = sprintf('[%.3f %.3f] 0 d', $black * $this->k, $white * $this->k);
        } else {
            $s = '[] 0 d';
        }
        $this->_out($s);
    }

    /**
     * Draws text within a box defined by width = w, height = h, and aligns
     * the text vertically within the box ($valign = M/B/T for middle, bottom, or top)
     * Also, aligns the text horizontally ($align = L/C/R/J for left, centered, right or justified)
     * drawTextBox uses drawRows
     *
     * This function is provided by TUFaT.com
     */
    function drawTextBox($strText, $w, $h, $align = 'L', $valign = 'T', $border = 1)
    {
        $xi = $this->GetX();
        $yi = $this->GetY();

        $hrow = $this->FontSize;
        $textrows = $this->drawRows($w, $hrow, $strText, 0, $align, 0, 0, 0);
        $maxrows = floor($h / $this->FontSize);
        $rows = min($textrows, $maxrows);

        $dy = 0;
        if (strtoupper($valign) == 'M')
            $dy = ($h - $rows * $this->FontSize) / 2;
        if (strtoupper($valign) == 'B')
            $dy = $h - $rows * $this->FontSize;

        $this->SetY($yi + $dy, false);
        $this->SetX($xi);

        $this->drawRows($w, $hrow, $strText, 0, $align, 0, $rows, 1);

        if ($border == 1)
            $this->Rect($xi, $yi, $w, $h);
    }

    function drawRows($w, $h, $txt, $border = 0, $align = 'J', $fill = 0, $maxline = 0, $prn = 0)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->clMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
            } else {
                $b2 = '';
                if (is_int(strpos($border, 'L')))
                    $b2 .= 'L';
                if (is_int(strpos($border, 'R')))
                    $b2 .= 'R';
                $b = is_int(strpos($border, 'T')) ? $b2 . 'T' : $b2;
            }
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while ($i < $nb) {
            //Get next character
            $c = $s[$i];
            if ($c == "\n") {
                //Explicit line break
                if ($this->getFontSpacing() > 0) {
                    $this->setFontSpacing();
                    if ($prn == 1)
                        $this->_out('0 Tw');
                }
                if ($prn == 1) {
                    $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                }
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border and $nl == 2)
                    $b = $b2;
                if ($maxline && $nl > $maxline)
                    return substr($s, $i);
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                //Automatic line break
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                    if ($this->getFontSpacing() > 0) {
                        $this->setFontSpacing();
                        if ($prn == 1)
                            $this->_out('0 Tw');
                    }
                    if ($prn == 1) {
                        $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                    }
                } else {
                    if ($align == 'J') {
                        $this->setFontSpacing(($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0);
                        if ($prn == 1)
                            $this->_out(sprintf('%.3f Tw', $this->getFontSpacing() * $this->k));
                    }
                    if ($prn == 1) {
                        $this->Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
                    }
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border and $nl == 2)
                    $b = $b2;
                if ($maxline && $nl > $maxline)
                    return substr($s, $i);
            } else
                $i++;
        }
        //Last chunk
        if ($this->getFontSpacing() > 0) {
            $this->setFontSpacing(0);
            if ($prn == 1)
                $this->_out('0 Tw');
        }
        if ($border and is_int(strpos($border, 'B')))
            $b .= 'B';
        if ($prn == 1) {
            $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
        }
        $this->x = $this->lMargin;
        return $nl;
    }

    function Row($data, $fill = false, $border = 0, $line_height = 5, $style = null)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->getPageWidth($i), $data[$i]));
        }
        $h = $line_height * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = $this->aligns[$i] ?? 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            if ($border)
                $this->Rect($x, $y, $w, $h);
            if ($style != null) {
                $this->SetFont("times", $style[$i]);
            }
            //Print the text
            $this->MultiCell($w, $line_height, $data[$i], 0, $a, $fill);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function GetMultiCellHeight($w, $h, $txt, $border = null, $align = 'J')
    {
        // Calculate MultiCell with automatic or explicit line breaks height
        // $border is un-used, but I kept it in the parameters to keep the call
        //   to this function consistent with MultiCell()
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->clMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $height = 0;
        while ($i < $nb) {
            // Get next character
            $c = $s[$i];
            if ($c == "\n") {
                // Explicit line break
                if ($this->getFontSpacing() > 0) {
                    $this->setFontSpacing(0);
                    $this->_out('0 Tw');
                }
                //Increase Height
                $height += $h;
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                // Automatic line break
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                    if ($this->getFontSpacing() > 0) {
                        $this->setFontSpacing();
                        $this->_out('0 Tw');
                    }
                    //Increase Height
                    $height += $h;
                } else {
                    if ($align == 'J') {
                        $this->setFontSpacing(($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0);
                        $this->_out(sprintf('%.3F Tw', $this->getFontSpacing() * $this->k));
                    }
                    //Increase Height
                    $height += $h;
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
            } else
                $i++;
        }
        // Last chunk
        if ($this->getFontSpacing() > 0) {
            $this->setFontSpacing();
            $this->_out('0 Tw');
        }
        //Increase Height
        $height += $h;

        return $height;
    }

    function mostrarLinha($label, $value, $fill = false, $top = false, $bottom = false, $label_offset = 45, $align = "L", $border = 0, $line_height = 5)
    {
        $b = ($bottom ? 'B' : '');
        $t = ($top ? 'T' : '');
        $this->SetFillColor(246, 246, 246);
        $this->SetFont("times", "B", "10");
        $height = $this->GetMultiCellHeight(0, $line_height, $value, $t . $b);
        $this->Cell($label_offset, $height, $label, $border, 0, "L", $fill);
        $this->SetFont("times", "", "10");
        $this->MultiCell(0, $line_height, $value, $border, 1, $fill, $align);
    }

    /**
     * Função que imprime todas as assinaturas necessárias na impresssão de um contrato
     */
    function imprimirAssinaturas($assinaturas)
    {
        if (count($assinaturas) > 0) {
            $this->CheckPageBreak(25);
            $this->Ln(15);
            $this->SetFont('times', 'B', 8);
            // Imprime assinaturas
            $y = $this->GetY();
            $aux = 1;
            foreach ($assinaturas as $assinatura) {
                if ($aux == 2) {
                    $this->SetXY(120, $y < $y2 ? $y : $y2);
                } elseif ($aux == 3) {
                    $this->CheckPageBreak(25);
                    $this->Ln(15);
                    $this->Cell(10, 10, "", 0, 1);
                    $aux = 1;
                }
                //parent::Ln(18);
                $y = $this->GetY();

                $this->MultiCell(80, 4, (!empty($assinatura['nome']) ? $assinatura['nome'] . "\n" : '') . $assinatura['cargo'], 'T', 'C');
                $y2 = $this->GetY();
                $aux++;
            }
        }
    }

}
