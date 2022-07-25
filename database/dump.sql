-- --- --- --- --- --- --- --- --- --- ---
-- Banco de dados
-- --- --- --- --- --- --- --- --- --- ---

# DROP DATABASE IF EXISTS vox;
CREATE DATABASE IF NOT EXISTS vox
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE vox;


-- --- --- --- --- --- --- --- --- --- ---
-- Configuração
-- --- --- --- --- --- --- --- --- --- ---

# SET FOREIGN_KEY_CHECKS = 0;


-- --- --- --- --- --- --- --- --- --- ---
-- Tabela
-- --- --- --- --- --- --- --- --- --- ---

# DROP TABLE IF EXISTS comunicacao;
CREATE TABLE IF NOT EXISTS comunicacao
(
  id                          INT         NOT NULL AUTO_INCREMENT,
  ws                          INT(2)      NOT NULL,
  json                        JSON        NOT NULL,
  controle_orgao_id           INT(4)      NOT NULL,
  documento_protocolo_redesim CHAR(13)    NOT NULL,
  documento_tipo_modelo       INT         NOT NULL,
  documento_situacao          INT         NOT NULL DEFAULT 0,
  documento_evento_data       DATE        NOT NULL,
  ip                          VARCHAR(15) NOT NULL,
  criado_em                   TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id)
) ENGINE = InnoDB
  DEFAULT CHARSET utf8mb4;

# DROP TABLE IF EXISTS ws32;
CREATE TABLE IF NOT EXISTS ws32
(
  id        INT       NOT NULL AUTO_INCREMENT,
  orgao     TEXT      NOT NULL,
  protocolo TEXT      NOT NULL,
  valor     TEXT      NOT NULL,
  guia      TEXT      NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id)
) ENGINE = InnoDB
  DEFAULT CHARSET utf8mb4;

# DROP TABLE IF EXISTS ws33;
CREATE TABLE IF NOT EXISTS ws33
(
  id        INT       NOT NULL AUTO_INCREMENT,
  orgao     TEXT      NOT NULL,
  consulta  TEXT      NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id)
) ENGINE = InnoDB
  DEFAULT CHARSET utf8mb4;
