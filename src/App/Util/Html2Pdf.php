<?php

/**********************************/
/***Última Alteração: 03/02/2023***/
/*************André****************/

namespace App\Util;

use App\Controller\IndexController;
use Mpdf\Mpdf;
use const APP_PATH;

class Html2Pdf extends Mpdf
{
    protected $cliente;
    private $fontFamily;

    public function __construct($title, $orientation = 'P')
    {
        $this->cliente = IndexController::getClienteConfig();
        $this->fontFamily = 'Arial';
        $this->SetTitle($title);
        parent::__construct([
            'mode' => 'utf-8',
            'orientation' => $orientation
        ]);
        $this->SetHTMLFooter('<hr/><table width="100%" style="font-size:12px">
    <tr>
        <td style="text-align: right;">Página {PAGENO}/{nbpg}</td>
    </tr>
</table>');
    }

    /**
     * Define um cabeçalho para todos os relatórios
     */
    function Header($content = "")
    {
        $this->Image(APP_PATH . 'assets/img/brasao.png', 40, 10, 20, 20);
        $this->SetFont($this->fontFamily, 'B', 14);
        $this->SetXY(65, 10);
        $this->Cell(70, 5, $this->cliente['nome'], 0, 2, 'L');
        $this->SetFont($this->fontFamily, 'B', 12);
        $this->Cell(70, 5, "ESTADO DE MATO GROSSO DO SUL", 0, 2, 'L');
        $this->SetFont($this->fontFamily, '', 8);
        $this->Cell(70, 4, $this->cliente['endereco'] . '.', 0, 2, 'L');
        $this->Cell(70, 3, 'CNPJ: ' . $this->cliente['cnpj'] . " / Telefone: " . $this->cliente['telefone'], 0, 0, 'L');
        $this->Cell(0, 5, '', 0, 2, "R");
        $this->Ln(10);
        //$this->SetDrawColor(211, 211, 211);
        //$this->WriteHTML("<hr/>");
    }

    function getFontFamiy()
    {
        return $this->fontFamily;
    }

    function getCliente()
    {
        return $this->cliente;
    }

}
