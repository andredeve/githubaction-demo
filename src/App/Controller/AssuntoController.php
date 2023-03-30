<?php

namespace App\Controller;

use App\Model\Assunto;
use Core\Controller\AppController;
use Core\Util\Report;

/**
 * Classe AssuntoController
 * @version 1.0
 * @author Anderson Brandão Batistoti <anderson@lxtec.com.br>
 * @date   08/01/2018
 * @copyright (c) 2018, Lxtec Informática
 */
class AssuntoController extends AppController
{
    public function __construct()
    {
        parent::__construct(get_class());
        $this->text_method = "getDescricao";
        $this->list_method = "listarAtivos";
    }

    /**
     * Método para imprimir lista de usuário cadastrados
     */
    public function imprimir()
    {
        $report = new Report("Relatório de Setores cadastrados");
//        $report->AliasNbPages();
        $report->AddPage();
        $report->SetFont("times", "B", "12");
        $report->Cell(0, 10, 'Relaçao de assuntos cadastrados no sistema:', 0, 1);
        $report->SetFont("times", "B", "9");
        $report->SetAligns(array('C', 'C', 'L', 'L', 'C', 'C', 'C'));
        $report->SetWidths(array(10, 10, 40, 40, 30, 30, 30)); //Total 190 para Retrato
        $report->setTableHeader(array('Cód.', 'Ativo', 'Nome', 'Sub-assunto de', 'Prazo', 'Dt.Cadastro', 'Última alteração'));
        $assunto = new Assunto();
        $fill = false;
        $report->SetFillColor(224, 235, 255);
        $report->SetFont("times", "", "8");
        foreach ($assunto->listar() as $assunto) {
            $ultimaAlteracao = $assunto->getUltimaAlteracao();
            $report->Row(
                array(
                    $assunto->getId(),
                    $assunto->getIsAtivo() ? 'Sim' : 'Não',
                    $assunto->getDescricao(),
                    $assunto->getAssuntoPai(),
                    $assunto->getPrazo() . " dia(s) " . ($assunto->getIsPrazoDiaUtil() ? "úteis" : "corridos"),
                    $assunto->getDataCadastro()->format('d/m/Y'),
                    !empty($ultimaAlteracao) ? $ultimaAlteracao->format('d/m/Y H:i:s') : 'Não registrada'
                ), false, true
            );
            $fill = !$fill;
        }
        $report->Ln(5);
        $report->Output();
    }


    public function inserir()
    {
        $this->setAssunto();
        parent::inserir();
    }

    function atualizar()
    {
        $this->setAssunto();
        parent::atualizar();
    }

    private function setAssunto()
    {
        $_POST['assuntoPai'] = !empty($_POST['assunto_pai_id']) ? (new Assunto())->buscar($_POST['assunto_pai_id']) : null;
    }
}
