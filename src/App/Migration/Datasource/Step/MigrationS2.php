<?php

namespace App\Migration\Datasource\Step;

use Core\Controller\AppController;
use Core\Model\MigrationHelper;
use PDO;

class MigrationS2 extends MigrationHelper
{
    private $databaseNameOld;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->databaseNameOld = AppController::getDatabaseConfig()["db_name_old"];
    }

    function run()
    {
        echo "Criando tabela \"pessoa\"...";
        $this->criarTabelaPessoa();
        echo " Concluído." . PHP_EOL;
        echo "Executando alterações na tabela \"usuario\"...";
        $this->atualizarTabelaUsuario();
        echo "Concluído." . PHP_EOL;
        echo "Criando cadastros de pessoas à partir de cadastros de interessados...";
        $this->importarPessoasInteressadas();
        echo "Concluído." . PHP_EOL;
        echo "Criando cadastros de pessoas à partir de usuários sem cadastro de interessado...";
        $this->importarPessoasUsuariosSemInteressados();
        echo "Concluído." . PHP_EOL;
        echo "Relacionando cadastro de interessados com pessoas...";
        $this->relacionarInteressadosComPessoas();
        echo "Concluído." . PHP_EOL;
        echo "Relacionando cadastro de usuários com relacionamento com interessados com o registro de pessoas...";
        $this->relacionarUsuariosInteressadosComPessoas();
        echo "Concluído." . PHP_EOL;
    }

    public function validate(): bool
    {
        $app_version = floatval(AppController::getConfig("app_version"));
        return $app_version <= 1.18;
    }

    private function criarTabelaPessoa() {
        $ddl = "CREATE TABLE IF NOT EXISTS `pessoa` (" .
            "`id` int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL," .
            "`endereco_id` int(11) DEFAULT NULL," .
            "`nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL," .
            "`shadow_nome` longtext COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`cnpj` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`ie` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`telefone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`celular` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`cpf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL UNIQUE," .
            "`rg` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL UNIQUE," .
            "`nacionalidade` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`data_nascimento` date DEFAULT NULL," .
            "`sexo` enum('m','f') COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`tipo_pessoa` enum('fisica','juridica') COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`estado_civil` enum('solteiro','casado','separado','divorciado','viuvo') COLLATE utf8_unicode_ci DEFAULT NULL," .
            "`ultima_alteracao` datetime DEFAULT NULL," .
            "`data_cadastro` date NOT NULL" .
            ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $this->db->exec($ddl);
    }

    private function atualizarTabelaUsuario() {
//        TODO: Testar cascata.
        $ddl = "ALTER TABLE usuario " .
            "DROP COLUMN nome, " .
            "DROP COLUMN telefone, " .
            "DROP FOREIGN KEY FK_2265B05D4FA2643F, " .
            "DROP COLUMN interessado_id, " .
            "ADD COLUMN pessoa_id INT;";
        $this->db->exec($ddl);
        $ddl = "ALTER TABLE interessado " .
            "ADD COLUMN pessoa_id INT, " .
            "ADD CONSTRAINT IDX_CBCBA86FDF6FA0A5 FOREIGN KEY (pessoa_id) REFERENCES pessoa(id) ON UPDATE CASCADE ON DELETE CASCADE";
        $this->db->exec($ddl);
    }

    private function importarPessoasInteressadas() {
        $instruction = "INSERT INTO pessoa(" .
            "endereco_id," .
            "nome," .
            "shadow_nome," .
            "cnpj," .
            "cpf," .
            "ie," .
            "rg," .
            "email," .
            "telefone," .
            "celular," .
            "nacionalidade," .
            "data_nascimento," .
            "sexo," .
            "tipo_pessoa," .
            "estado_civil," .
            "ultima_alteracao," .
            "data_cadastro" .
            ") SELECT " .
            "MI.endereco_id," .
            "MI.nome," .
            "MI.shadow_nome, " .
            "MI.cnpj," .
            "MI.cpf," .
            "MI.ie," .
            "MI.rg," .
            "MI.email," .
            "MI.telefone," .
            "MI.celular," .
            "MI.nacionalidade," .
            "MI.data_nascimento," .
            "MI.sexo," .
            "MI.tipoPessoa," .
            "MI.estadoCivil," .
            "MI.ultima_alteracao," .
            "MI.data_cadastro FROM $this->databaseNameOld.interessado MI";
        $this->db->exec($instruction);
    }

    private function importarPessoasUsuariosSemInteressados() {
        $query = "SELECT MU.id, MU.nome, MU.email, MU.telefone, MU.celular, MU.ultima_alteracao, MU.data_cadastro " .
            "FROM $this->databaseNameOld.usuario MU WHERE MU.interessado_id IS NULL";
        echo $query . PHP_EOL;
        $result = $this->db->query($query)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $usuario) {
            $query = "INSERT INTO pessoa(" .
                "nome, " .
                "email, " .
                "telefone, " .
                "celular, " .
                "ultima_alteracao," .
                "data_cadastro" .
                ") VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$usuario["nome"], $usuario["email"], $usuario["telefone"], $usuario["celular"], $usuario["ultima_alteracao"], $usuario["data_cadastro"]]);
            $pessoa_id = $this->db->lastInsertId();
            $usuario_id = $usuario['id'];
            $dml = "UPDATE usuario SET `pessoa_id` = ? WHERE `id` = ?;";
            $stmt = $this->db->prepare($dml);
            if (!$stmt->execute([$pessoa_id, $usuario_id])) {
                echo "FALHA: Não foi possível relacionar o usuário #$usuario_id com a pessoa #$pessoa_id:" . PHP_EOL;
                $queryString = $stmt->queryString;
                $limit = 1;
                $queryString = str_replace("?", $pessoa_id, $queryString, $limit);
                $queryString = str_replace("?", $usuario_id, $queryString, $limit);
                echo $queryString . PHP_EOL;
            }
        }
    }

    private function relacionarInteressadosComPessoas() {
        $instruction = "UPDATE interessado I
                        INNER JOIN $this->databaseNameOld.interessado MI
                        ON I.id = MI.id
                        INNER JOIN pessoa P
                        ON P.endereco_id = MI.endereco_id
                        SET I.pessoa_id = P.id
                        WHERE I.pessoa_id IS NULL";
        $this->db->exec($instruction);
    }

    private function relacionarUsuariosInteressadosComPessoas() {
        $instruction = "UPDATE usuario U
                        INNER JOIN $this->databaseNameOld.usuario MU
                        ON MU.id = U.id
                        INNER JOIN interessado I
                        ON MU.interessado_id = I.id
                        INNER JOIN pessoa P
                        ON P.id = I.pessoa_id
                        SET U.pessoa_id = P.id
                        WHERE U.pessoa_id IS NULL";
        $this->db->exec($instruction);
    }
}