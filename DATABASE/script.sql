CREATE DATABASE `db_sistema_controle_servico`;

USE `db_sistema_controle_servico`;

-- TABELA UTILIZADA PARA SALVAR OS USUÁRIOS QUE VÃO EFETUAR O LOGIN NO SISTEMA
CREATE TABLE `user` (
    `id_user` BIGINT(20) AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(120) NOT NULL,
    `password` VARCHAR(60) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP(),
    `ativo` TINYINT(1) NULL DEFAULT 1,
    `session_token` VARCHAR(255) NULL,
    PRIMARY KEY (`id_user`),
    UNIQUE KEY `unq_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `service` (
    `id_service` BIGINT(20) AUTO_INCREMENT NOT NULL,
    `description` VARCHAR(45) NULL,
    `price` DECIMAL(11,3) NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP(),
    `finished_at` DATETIME NULL,
    `commission_user` DECIMAL(11,3) NULL,
    `user_id_user` BIGINT(20) NOT NULL,
    PRIMARY KEY (`id_service`),
    CONSTRAINT `fk_service_user` 
        FOREIGN KEY (`user_id_user`) 
        REFERENCES `user`(`id_user`) 
        ON UPDATE CASCADE 
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
