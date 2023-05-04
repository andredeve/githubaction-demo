-- Processos
CREATE OR REPLACE VIEW view_processos AS
SELECT p.*,p.id as processo_id_view,
       CONCAT(p.numero, "/", p.exercicio) as processo,
       so.nome                            as setor_origem,
       ipes.cpf,
       ipes.cnpj,
       ipes.nome                          as interessado,
       i.is_externo                       as is_interessado_externo,
       uapes.nome                         as usuario_abertura,
       a.nome                             as assunto,
       a.is_externo                       as is_assunto_externo,
       `a`.`assunto_pai_id`               as `assunto_pai_id`,
       st.cor                             as cor_status,
       st.id                              as status_id,
       st.descricao                       as status_processo,
       sant.nome                          as setor_anterior,
       t.setor_atual_id,
       sa.nome                            as setor_atual,
       t.is_despachado,
       t.data_vencimento                  as data_vencimento_tramite,
       t.id                               as tramite_id,
       t.remessa_id,
       t.usuario_destino_id,
       t.usuario_envio_id,
       t.parecer,
       t.is_recebido,
       t.data_envio,
       f.id as fluxograma_id,
       IFNULL(
            (
                SELECT a.novo_vencimento_processo
                FROM anexo a
                WHERE a.processo_id = p.id AND a.novo_vencimento_processo IS NOT NULL
                    ORDER BY a.data DESC LIMIT 1 
            ), p.data_vencimento 
        )                                  as data_vencimento_atualizada
       FROM processo p
       LEFT JOIN assunto a ON a.id = p.assunto_id
       LEFT JOIN tramite t ON t.processo_id=p.id AND t.numero_fase=p.numero_fase AND t.assunto_id=p.assunto_id AND t.is_cancelado=0 AND t.is_despachado=0
       LEFT JOIN setor sa ON sa.id = t.setor_atual_id
       LEFT JOIN setor sant ON sant.id = t.setor_anterior_id
       LEFT JOIN fluxograma f ON f.assunto_id = a.id
       LEFT JOIN status_processo st ON st.id = t.status_id
       JOIN interessado i ON i.id = p.interessado_id
       JOIN usuario ua ON ua.id = p.usuario_abertura_id
       JOIN pessoa uapes ON uapes.id = ua.pessoa_id
       JOIN pessoa ipes ON ipes.id = i.pessoa_id
       JOIN setor so ON so.id = p.setor_origem_id;

-- Anexos
CREATE OR REPLACE VIEW view_anexos AS
SELECT a.*, t.descricao as tipo, CONCAT(p.numero, "/", p.exercicio) as processo
FROM anexo a
         JOIN processo p ON p.id = a.processo_id
         JOIN tipo_anexo t ON t.id = a.tipo_anexo_id;

-- Arquivo Físico
CREATE OR REPLACE VIEW view_arquivo_fisico AS
SELECT l.*,
       lf.descricao  as local,
       t.descricao   as tipo_local,
       sub.descricao as subtipo_local,
       upes.nome     as usuario_cadastro,
       uapes.nome    as usuario_alteracao,
       p.id          as processo_id
FROM localizacao_fisica l
       JOIN local_fisico lf ON lf.id = l.local_id
       JOIN tipo_local_fisico t ON t.id = l.tipo_local_id
       JOIN subtipo_local_fisico sub ON sub.id = l.subtipo_local_id
       LEFT JOIN usuario u ON u.id = l.usuario_id
       JOIN pessoa upes ON upes.id = u.pessoa_id
       LEFT JOIN usuario ua ON ua.id = l.usuario_alteracao_id
       JOIN pessoa uapes ON uapes.id = ua.pessoa_id
       LEFT JOIN processo p ON p.local_fisico_id = l.id;

CREATE OR REPLACE VIEW view_solicitacoes AS
SELECT
    `s`.`id` AS `id`,
    `pe`.`nome` AS `solicitante`,
    `s`.`motivo` AS `motivo`,
    `s`.`tipo` AS `tipo`,
    `s`.`status` AS `status`,
    `s`.`data` AS `data`,
    CONCAT(`p`.`numero`, '/', `p`.`exercicio`) AS `processo`,
    CONCAT(`a`.`numero`, '/', `a`.`exercicio`) AS `documento_anterior`,
    CONCAT(`a2`.`numero`, '/', `a2`.`exercicio`) AS `documento_novo`,
    `a`.`processo_id`,
    `s`.`solicitante` AS `solicitante_id`
FROM `solicitacao` `s`
         LEFT JOIN `usuario` `u`
                   ON `s`.`solicitante` = `u`.`id`
         LEFT JOIN `pessoa` `pe`
                   ON `pe`.`id` = `u`.`pessoa_id`
         LEFT JOIN `anexo` `a`
                   ON `s`.`anexo_anterior_id` = `a`.`id`
         LEFT JOIN `anexo` `a2`
                   ON `s`.`anexo_novo_id` = `a2`.`id`
         LEFT JOIN processo `p`
                   ON `p`.id = `a`.processo_id;




CREATE OR REPLACE VIEW view_solicitacoes AS
    SELECT
        `s`.`id` AS `id`,
        `pessoa`.`nome` AS `solicitante`,
        `s`.`motivo` AS `motivo`,
        `s`.`tipo` AS `tipo`,
        `s`.`status` AS `status`,
        `s`.`data` AS `data`,
        CONCAT(`p`.`numero`, '/', `p`.`exercicio`) AS `processo`,
        CONCAT(`a`.`numero`, '/', `a`.`exercicio`) AS `documento_anterior`,
        CONCAT(`a2`.`numero`, '/', `a2`.`exercicio`) AS `documento_novo`,
        `a`.`processo_id`,
        `s`.`solicitante` AS `solicitante_id`
    FROM
        `solicitacao` `s`
            LEFT JOIN `usuario` `u`
                ON `s`.`solicitante` = `u`.`id`
            LEFT JOIN `anexo` `a`
                ON `s`.`anexo_anterior_id` = `a`.`id`
            LEFT JOIN `anexo` `a2`
                ON `s`.`anexo_novo_id` = `a2`.`id`
            JOIN `pessoa`
                ON `pessoa`.`id` = `u`.`pessoa_id`
            LEFT JOIN processo `p`
                ON `p`.id = `a`.processo_id;


CREATE OR REPLACE VIEW view_interessados
AS SELECT interessado.id,
          pessoa.nome,
          pessoa.cpf,
          pessoa.cnpj,
          pessoa.shadow_nome,
          pessoa.tipo_pessoa,
          interessado.pessoa_id,
          interessado.is_ativo
     FROM pessoa
     JOIN interessado ON interessado.pessoa_id = pessoa.id;

-- Correção datas de envio nulas
-- UPDATE tramite t JOIN processo p ON t.processo_id = p.id AND t.data_envio > p.data_abertura AND
--                                    YEAR(t.data_envio) = 2019 AND YEAR(p.data_abertura) < 2019
-- SET t.data_envio= NULL;

# -- Busca Fonética
# DROP FUNCTION IF EXISTS transformar_fonetica;
#
# DELIMITER $$
# CREATE FUNCTION transformar_fonetica(ptexto TEXT)
#   RETURNS TEXT
# BEGIN
#   DECLARE vtexto TEXT;
#   DECLARE vtexto_apoio TEXT;
#   DECLARE vposicao_atual INT;
#   DECLARE vcaracter_anterior VARCHAR(1);
#   DECLARE vcaracter_atual VARCHAR(1);
#   DECLARE vcaracter_seguinte VARCHAR(1);
#   DECLARE vsom VARCHAR(2);
#   DECLARE com_acentos VARCHAR(65);
#   DECLARE sem_acentos VARCHAR(65);
#
#   SET vtexto = UPPER(ptexto);
#
#   SET com_acentos = 'ŠšŽžÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝŸÞàáâãäåæçèéêëìíîïñòóôõöøùúûüýÿþƒ';
#   SET sem_acentos = 'SsZzAAAAAAACEEEEIIIINOOOOOOUUUUYYBaaaaaaaceeeeiiiinoooooouuuuyybf';
#   SET vposicao_atual = LENGTH(com_acentos);
#
#   -- Remove acentos
#   WHILE vposicao_atual > 0 DO
#   SET vtexto = REPLACE(vtexto, SUBSTRING(com_acentos, vposicao_atual, 1), SUBSTRING(sem_acentos, vposicao_atual, 1));
#   SET vposicao_atual = vposicao_atual - 1;
#   end while;
#
#   -- Remove caracteres inválido
#   SET vposicao_atual = 1;
#
#   WHILE vposicao_atual <= LENGTH(vtexto) DO
#   SET vcaracter_atual = SUBSTRING(vtexto, vposicao_atual, 1);
#
#   IF INSTR('ABCDEFGHIJKLMNOPQRSTUVWXYZ ', vcaracter_atual) <> 0 THEN
#     SET vtexto_apoio = CONCAT(IFNULL(vtexto_apoio, ''), vcaracter_atual);
#   END IF;
#
#   SET vposicao_atual = vposicao_atual + 1;
#   END WHILE;
#
#   SET vtexto = vtexto_apoio;
#
#   -- Substitui os mais simples
#   SET vtexto = REPLACE(vtexto, 'SS', 'S');
#   SET vtexto = REPLACE(vtexto, 'SH', 'X');
#   SET vtexto = REPLACE(vtexto, 'XC', 'S');
#   SET vtexto = REPLACE(vtexto, 'QU', 'K');
#   SET vtexto = REPLACE(vtexto, 'CH', 'X');
#   SET vtexto = REPLACE(vtexto, 'PH', 'F');
#   SET vtexto = REPLACE(vtexto, 'LH', 'LI');
#
#   -- Remove duplicados
#   SET vposicao_atual = 1;
#   SET vtexto_apoio = '';
#
#   WHILE vposicao_atual <= LENGTH(vtexto) DO
#   SET vcaracter_atual = SUBSTRING(vtexto, vposicao_atual, 1);
#
#   IF vposicao_atual < LENGTH(vtexto) THEN
#     SET vcaracter_seguinte = SUBSTRING(vtexto, vposicao_atual + 1, 1);
#   ELSE -- Último caracter não tem motivo para ser verificado
#     SET vcaracter_seguinte = '';
#   END IF;
#
#   IF vcaracter_atual <> vcaracter_seguinte THEN
#     SET vtexto_apoio = CONCAT(vtexto_apoio, vcaracter_atual);
#   END IF;
#
#   SET vposicao_atual = vposicao_atual + 1;
#   END WHILE;
#
#   SET vtexto = vtexto_apoio;
#
#   -- Troca caracteres pelo som
#   SET vposicao_atual = 1;
#   SET vtexto_apoio = '';
#
#   WHILE vposicao_atual <= LENGTH(vtexto) DO
#   SET vcaracter_atual = SUBSTRING(vtexto, vposicao_atual, 1);
#
#   IF vposicao_atual < LENGTH(vtexto) THEN
#     SET vcaracter_seguinte = SUBSTRING(vtexto, vposicao_atual + 1, 1);
#   ELSE
#     SET vcaracter_seguinte = '';
#   END IF;
#
#   -- "B" seguindo de qualquer caracter que não seja "A", "E", "I", "O", "U", "R" ou "Y"
#   IF vcaracter_atual = 'B' AND INSTR('AEIOURY', vcaracter_seguinte) = 0 THEN
#     SET vsom = 'BI';
#     -- "C" seguindo de "E", "I" ou "Y"
#   ELSEIF vcaracter_atual = 'C' AND INSTR('EIY', vcaracter_seguinte) <> 0 THEN
#     SET vsom = 'S';
#   ELSEIF vcaracter_atual = 'C' THEN
#     SET vsom = 'K';
#   ELSEIF vcaracter_atual = 'D' AND INSTR('AEIOURY', vcaracter_seguinte) = 0 THEN
#     SET vsom = 'DI';
#   ELSEIF vcaracter_atual = 'E' THEN
#     SET vsom = 'I';
#   ELSEIF vcaracter_atual = 'G' AND INSTR('EIY', vcaracter_seguinte) <> 0 THEN -- GE, GI OU GY
#     SET vsom = 'J';
#   ELSEIF vcaracter_atual = 'G' AND vcaracter_seguinte = 'T' THEN -- GT
#     SET vsom = '';
#   ELSEIF vcaracter_atual = 'H' THEN
#     SET vsom = 'H';
#   ELSEIF vcaracter_atual = 'N' THEN
#     SET vsom = 'M';
#   ELSEIF vcaracter_atual = 'P' AND INSTR('AEIOURY', vcaracter_seguinte) = 0 THEN
#     SET vsom = 'PI';
#   ELSEIF vcaracter_atual = 'Q' THEN
#     SET vsom = 'K';
#     -- QUA, QUE, QUI, QUO ou QUY
#   ELSEIF IFNULL(vcaracter_anterior, '') = 'Q' AND vcaracter_atual = 'U' AND INSTR('AEIOY', vcaracter_seguinte) <> 0 THEN
#     SET vsom = '';
#   ELSEIF vcaracter_atual = 'W' THEN
#     SET vsom = 'V';
#   ELSEIF vcaracter_atual = 'X' THEN
#     SET vsom = 'S';
#   ELSEIF vcaracter_atual = 'Y' THEN
#     SET vsom = 'I';
#   ELSEIF vcaracter_atual = 'Z' THEN
#     SET vsom = 'S';
#   ELSE
#     SET vsom = vcaracter_atual;
#   END IF;
#
#   SET vcaracter_anterior = vcaracter_atual;
#   SET vposicao_atual = vposicao_atual + 1;
#   SET vtexto_apoio = CONCAT(vtexto_apoio, vsom);
#   END WHILE;
#
#   SET vtexto = vtexto_apoio;
#
#   SET vtexto = CONCAT('%', replace(vtexto, ' ', '%'), '%');
#
#   RETURN vtexto;
# END
# $$
# DELIMITER ;
#
# -- Criação da trigger para atualização Fonética
# DELIMITER $
# CREATE TRIGGER interessado_fonetica
#   AFTER UPDATE
#   ON interessado
#   FOR EACH ROW
# BEGIN
#   if NEW.nome <=> OLD.nome THEN
#     UPDATE interessado
#     SET fonetica = transformar_fonetica(NEW.nome)
#     WHERE id = OLD.id;
#   END IF;
# END;
# $