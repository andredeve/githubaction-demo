<?php

namespace Core\Util\Migration\Step;

use Core\Controller\AppController;
use Core\Exception\TechnicalException;
use Core\Util\Migration\MigrationHelper;
use Exception;

class MigracaoS1 extends MigrationHelper
{

    /**
     * @return void
     * @throws Exception
     */
    public function run()
    {
        if (!$this->tabelaSolicitacaoExiste()) {
            $this->criarTabelaSolicitacao();
        } else if ($this->tabelaSolicitacaoNecessitaAtualizar()) {
            if (!$this->tabelaAnexoAlteracaoExiste()) {
                $this->criarTabelaAnexoAlteracao();
            }
            $this->atualizarTabelaSolicitacao();
        }
        $this->atualizarSolicitacoesView();
    }

    private function tabelaSolicitacaoExiste(): bool
    {
        echo "Verificando existência da tabela \"solicitacao\"... ";
        $sql_show_solicitacao = "SHOW TABLES LIKE 'solicitacao'";
        $result = $this->db->query($sql_show_solicitacao)->rowCount() > 0;
        if ($result) {
            echo "Tabela encontrada." . PHP_EOL;
        } else {
            echo "Tabela não encontrada." . PHP_EOL;
        }
        return $result;
    }

    private function tabelaAnexoAlteracaoExiste(): bool
    {
        echo "Verificando a exitência da tabela anexo_alteracao... ";
        $sql_show_solicitacao = "SHOW TABLES LIKE 'anexo_alteracao'";
        $result = $this->db->query($sql_show_solicitacao)->rowCount() > 0;
        if ($result) {
            echo "Tabela encontrada." . PHP_EOL;
        } else {
            echo "Tabela não encontrada." . PHP_EOL;
        }
        return $result;
    }

    /**
     * @throws TechnicalException
     */
    private function criarTabelaSolicitacao()
    {
        echo "Criando a tabela \"solicitacao\"... ";
        $sql_tabela_solicitacao = "CREATE TABLE solicitacao (
            id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            solicitante int(11) NOT NULL,
            motivo varchar(255) DEFAULT NULL,
            tipo enum('Edição','Exclusão') DEFAULT NULL,
            anexo_anterior_id int(11) DEFAULT NULL,
            anexo_novo_id int(11) DEFAULT NULL,
            status enum('Pendente','Aprovado','Recusado') DEFAULT NULL,
            data datetime NOT NULL DEFAULT current_timestamp(),
            modificado_em datetime NOT NULL DEFAULT current_timestamp(),
            INDEX IDX_A84F9E1677F7024F (solicitante),
            INDEX IDX_A84F9E1697E4C0D5 (anexo_anterior_id),
            INDEX IDX_A84F9E16784FCE69 (anexo_novo_id),
            INDEX IDX_A84F9E1677F7024F97E4C0D5784FCE69 (solicitante,anexo_anterior_id,anexo_novo_id),
            FOREIGN KEY (anexo_novo_id) REFERENCES anexo_alteracao(id) ON DELETE CASCADE,
            FOREIGN KEY (anexo_anterior_id) REFERENCES anexo(id) ON DELETE CASCADE,
            FOREIGN KEY (solicitante) REFERENCES usuario(id));";
        $result = $this->db->query($sql_tabela_solicitacao)->execute();
        if (!$result) {
            throw new TechnicalException("Erro: Não foi possível criar a tabela \"solicitacao\".");
        }
        echo "Tabela \"solicitacao\" criada." . PHP_EOL;
    }

    /**
     * @throws TechnicalException
     */
    private function criarTabelaAnexoAlteracao()
    {
        echo "Criando a tabela \"anexo_alteracao\"... ";
        $sql_tabela_anexo_alteracao = "CREATE TABLE anexo_alteracao (
            id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            tipo_anexo_id int(11) DEFAULT NULL,
            classificacao_id int(11) DEFAULT NULL,
            processo_id int(11) NOT NULL,
            usuario_id int(11) DEFAULT NULL,
            is_digitalizado tinyint(1) NOT NULL,
            is_auto_numeric tinyint(1) NOT NULL DEFAULT 0,
            numero varchar(255) DEFAULT NULL,
            exercicio varchar(255) DEFAULT NULL,
            dataValidade date DEFAULT NULL,
            descricao varchar(255) DEFAULT NULL,
            arquivo varchar(255) DEFAULT NULL,
            data date NOT NULL,
            data_cadastro datetime NOT NULL,
            valor decimal(10,2) DEFAULT NULL,
            qtde_paginas int(11) DEFAULT NULL,
            is_circulacao_interna tinyint(1) NOT NULL DEFAULT 0,
            UNIQUE KEY anexo_unique (processo_id,tipo_anexo_id,numero),
            INDEX IDX_B6495C641FF1DCAD (tipo_anexo_id),
            INDEX IDX_B6495C641AB36034 (classificacao_id),
            INDEX IDX_B6495C64AAA822D2 (processo_id),
            INDEX IDX_B6495C64DB38439E (usuario_id),
            INDEX exercicio_index (exercicio),
            FOREIGN KEY (classificacao_id) REFERENCES classificacao (id) ON DELETE CASCADE,
            FOREIGN KEY (tipo_anexo_id) REFERENCES tipo_anexo (id),
            FOREIGN KEY (processo_id) REFERENCES processo (id) ON DELETE CASCADE,
            FOREIGN KEY (usuario_id) REFERENCES usuario (id))";
        $result = $this->db->query($sql_tabela_anexo_alteracao)->execute();
        if ($result) {
            echo "Tabela criada." . PHP_EOL;
        } else {
            throw new TechnicalException("Não foi possível criar a tabela \"anexo_alteracao\".");
        }
    }

    private function tabelaSolicitacaoNecessitaAtualizar(): bool
    {
        echo "Verificando a necessidade de atualizar a tabela \"solicitacao\"... ";
        $sql_verificar_tabela_antiga = "SELECT column_name FROM information_schema.columns WHERE table_schema = '" . AppController::getDatabaseConfig()["db_name"] . "' AND table_name = 'solicitacao' AND column_name LIKE 'anexo_id';";
        $result = $this->db->query($sql_verificar_tabela_antiga)->rowCount() > 0;
        if ($result) {
            echo "Atualização requerida." . PHP_EOL;
        } else {
            echo "Atualização não requerida." . PHP_EOL;
        }
        return $result;
    }

    /**
     * @throws TechnicalException
     */
    private function atualizarTabelaSolicitacao()
    {
        echo "Atualizando a tabela \"solicitacao\"... ";
        $sql_tabela_solicitacao_novo = "CREATE TABLE solicitacao_novo (
            id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            solicitante int(11) NOT NULL,
            motivo varchar(255) DEFAULT NULL,
            tipo enum('Edição','Exclusão') DEFAULT NULL,
            anexo_anterior_id int(11) DEFAULT NULL,
            anexo_novo_id int(11) DEFAULT NULL,
            status enum('Pendente','Aprovado','Recusado') DEFAULT NULL,
            data datetime NOT NULL DEFAULT current_timestamp(),
            modificado_em datetime NOT NULL DEFAULT current_timestamp(),
            INDEX IDX_A84F9E1677F7024F (solicitante),
            INDEX IDX_A84F9E1697E4C0D5 (anexo_anterior_id),
            INDEX IDX_A84F9E16784FCE69 (anexo_novo_id),
            INDEX IDX_A84F9E1677F7024F97E4C0D5784FCE69 (solicitante,anexo_anterior_id,anexo_novo_id),
            FOREIGN KEY (anexo_novo_id) REFERENCES anexo_alteracao(id) ON DELETE CASCADE,
            FOREIGN KEY (anexo_anterior_id) REFERENCES anexo(id) ON DELETE CASCADE,
            FOREIGN KEY (solicitante) REFERENCES usuario(id))";
        $this->db->exec($sql_tabela_solicitacao_novo);
        $sql_copiar_dados = "INSERT INTO solicitacao_novo(id, solicitante, motivo, tipo, anexo_anterior_id, anexo_novo_id, status, data, modificado_em) (SELECT s.id, s.solicitante, s.motivo, 'Exclusão', s.anexo_id, null, s.finalizado, s.data_solicitacao, s.modificado_em FROM solicitacao s);";
        $this->db->exec($sql_copiar_dados);
        $sql_drop_solicitacao = "DROP TABLE solicitacao";
        $this->db->exec($sql_drop_solicitacao);
        $sql_rename_solicitacao = "RENAME TABLE solicitacao_novo TO solicitacao";
        $result = $this->db->exec($sql_rename_solicitacao);
        if ($result) {
            echo " Concluído." . PHP_EOL;
        } else {
            throw new TechnicalException("Ocorreu um erro durante a atualização da tabela \"solicitacao\".");
        }
    }

    /**
     */
    private function atualizarSolicitacoesView() {
        echo "Criando ou atualizando a view \"view_solicitacoes\"... ";
        $sql_solicitacoes_view = "CREATE OR REPLACE VIEW view_solicitacoes AS SELECT " .
            "`s`.`id` AS `id`, " .
            "`u`.`nome` AS `solicitante`, " .
            "`s`.`motivo` AS `motivo`, " .
            "`s`.`tipo` AS `tipo`, " .
            "`s`.`status` AS `status`, " .
            "`s`.`data` AS `data`, " .
            "CONCAT(`p`.`numero`, '/', `p`.`exercicio`) AS `processo`, " .
            "CONCAT(`a`.`numero`, '/', `a`.`exercicio`) AS `documento_anterior`, " .
            "CONCAT(`a2`.`numero`, '/', `a2`.`exercicio`) AS `documento_novo`, " .
            "`a`.`processo_id` " .
            "FROM `solicitacao` `s` " .
            "LEFT JOIN `usuario` `u` " .
            "ON `s`.`solicitante` = `u`.`id` " .
            "LEFT JOIN `anexo` `a` " .
            "ON `s`.`anexo_anterior_id` = `a`.`id` " .
            "LEFT JOIN `anexo` `a2` " .
            "ON `s`.`anexo_novo_id` = `a2`.`id` " .
            "LEFT JOIN processo `p` " .
            "ON `p`.id = `a`.processo_id;";
        $this->db->exec($sql_solicitacoes_view);
        echo "Concluído." . PHP_EOL;
    }
}