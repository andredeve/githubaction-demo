<?php

namespace App\Util;

use App\Controller\IndexController;
use App\Enum\TipoCampo;
use App\Model\Estado;
use App\Model\Tramite;
use Core\Util\Report;
use const APP_PATH;
use function mb_strtoupper;

/**
 * Classe FormularioEletronico
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   20/03/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class FormularioEletronico extends Report
{

    private $tramite;

    function __construct(Tramite $tramite, $orientation = 'P')
    {
        $this->tramite = $tramite;
        parent::__construct("Formulário Eletrônico #" . $tramite->getNumeroFase(), $orientation);
    }

    function Header()
    {
        $this->SetY($this->GetY() + 10, false);
        $this->SetLineWidth(0.1);
        //$this->SetDrawColor(211, 211, 211);
        $cliente = IndexController::getClienteConfig();
        $this->SetFont('times', '', 12);
        $imagePath = APP_PATH . 'assets/img/brasao.png';
        $imageInfo = getimagesize($imagePath);
        $ratio = $imageInfo[0] / $imageInfo[1];
        $width = $ratio * 20;
        $this->Image($imagePath, 20, $this->GetY() + 2.5, $width, 20);
        $this->drawTextBox('', 190, 25);
        $this->Cell(20);
        $estado = mb_convert_encoding((new Estado())->buscarPorUF($cliente['estado']), 'Windows-1252', 'UTF-8');
        $this->Cell(0, 7, 'ESTADO DE ' .mb_strtoupper($estado), 0, 2, 'C');
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 5,mb_strtoupper($cliente['nome'], 'UTF-8') , 0, 0, 'C');
        $this->Ln(17);
    }

    function gerar($dest, $file)
    {
        error_reporting(E_ERROR);
        $respostasPergunta = $this->tramite->getRespostasPergunta();
        $respostasCampo = $this->tramite->getRespostasCampo();
        $naoExibir = array(TipoCampo::ARQUIVO, TipoCampo::ARQUIVO_MULTIPLO);
        foreach ($respostasCampo as $i => $resposta) {
            if (in_array($resposta->getCampo()->getTipo(), $naoExibir)) {
                unset($respostasCampo[$i]);
            }
        }
        if (count($respostasPergunta) == 0 && count($respostasCampo) == 0) {
            return false;
        }
//        $this->AliasNbPages();
        $this->AddPage();

        $processo = $this->tramite->getProcesso();

        //// Linha Título
        $this->SetFont('times', 'B', 12);
        $y_inicial = $this->GetY() + 26;
        $this->SetY($y_inicial, false);
        $this->Cell(0, 7, 'Formulário Eletrônico', 0, 2, 'C');
        ///
        //// Linha Assunto
        $this->SetFont('times', 'B', 12);
        $y_inicial = $this->GetY()+2;
        $this->SetY($y_inicial, false);
        $this->Cell(0, 7, 'ASSUNTO', 'LTR', 2);
        $this->SetFont('times', '', 10);
        $this->MultiCell(0, 5, $processo->getAssunto(), 'LBR', 'L');
        ///
        /// Linha Setor e Fase
        $y_fase_setor = $this->GetY();
        $this->SetXY(10, $y_fase_setor);
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 7, 'Setor', 'LR', 2);
        $this->SetFont('times', '', 12);
        $this->Cell(0, 5, $this->tramite->getNumeroFase() == 1 ? $processo->getSetorOrigem() : $this->tramite->getSetorAtual(), 'LRB', 0, 'L');
        $y_ano = $this->GetY();
        $this->SetXY(160, $y_fase_setor);
        $this->SetFont('times', 'B', 12);
        $this->Cell(0, 7, 'Fase', 'LR', 2);
        $this->SetFont('times', '', 12);
        $this->Cell(0, 5, $this->tramite->getNumeroFase(), 'LRB', 0, 'L');

        $this->Ln();
        $font_size = 11;

        foreach ($respostasPergunta as $key => $resposta) {
            $this->SetY($this->GetY() + ($key === 0 ? 5 : .1), false);
            $this->SetFont("times", "B", $font_size);
            $this->Cell(0, 8, $resposta->getPerguntaTxt(), 1, 1, "L");
            $this->SetFont("times", "", $font_size);
            
            if ($resposta->getResposta() == 1){
                $respostaFormatada = 'Sim';
            } else if ($resposta->getResposta() == 0){
                $respostaFormatada = 'Não';
            } else {
                $respostaFormatada = 'Não respondida';
            }
            
            $this->Cell(0, 8, $respostaFormatada, 1, 1, "L",false, '$link');
        }

        foreach ($respostasCampo as $key => $resposta) {
            $this->SetY($this->GetY() + ($key === 0 ? 5 : .1), false);
            $this->SetFont("times", "B", $font_size);
            $this->Cell(0, 8, $resposta->getCampoTxt() . ":", 1, 1, "L");
            $this->SetFont("times", "", $font_size);
            $link = !empty($resposta->getProcessoLincado()) ? "(". APP_URL."Processo/editar/id/".$resposta->getProcessoLincado()->getId() .")" : null;

            $this->Cell(0, 8, $resposta->getResposta(), 1, 1, "L",false, '$link');
        }
        $this->Output($file, $dest);
//        \App\Controller\ComponenteController::inserirComponente($this->tramite->getProcesso(), null  , $this->tramite);
//        \App\Controller\ComponenteController::adicionarPaginacaoCarimbo($this->tramite->getProcesso(), $file  , $this->tramite);
        return true;
    }

}
