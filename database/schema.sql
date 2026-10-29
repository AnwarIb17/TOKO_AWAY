-- =====================================================================
--  AWAY FOTOCOPY — Skema Database
--  Import melalui phpMyAdmin, atau jalankan: mysql < schema.sql
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `away_fotocopy`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `away_fotocopy`;

DROP TABLE IF EXISTS `sale_items`;
DROP TABLE IF EXISTS `stock_movements`;
DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `expenses`;
DROP TABLE IF EXISTS `shifts`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `print_services`;
DROP TABLE IF EXISTS `finishings`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `users`;

-- ---------------------------------------------------------------------
CREATE TABLE `users` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100) NOT NULL,
    `username`      VARCHAR(50)  NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `settings` (
    `setting_key`   VARCHAR(64) NOT NULL,
    `setting_value` TEXT NULL,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(80)  NOT NULL,
    `slug`       VARCHAR(80)  NOT NULL,
    `sort_order` INT          NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `products` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` INT UNSIGNED NULL,
    `sku`         VARCHAR(50)  NULL,
    `barcode`     VARCHAR(50)  NULL,
    `name`        VARCHAR(150) NOT NULL,
    `unit`        VARCHAR(20)  NOT NULL DEFAULT 'pcs',
    `cost_price`  DECIMAL(12,2) NOT NULL DEFAULT 0,
    `sell_price`  DECIMAL(12,2) NOT NULL DEFAULT 0,
    `stock`       INT NOT NULL DEFAULT 0,
    `min_stock`   INT NOT NULL DEFAULT 5,
    `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_products_sku` (`sku`),
    KEY `idx_products_barcode` (`barcode`),
    KEY `idx_products_category` (`category_id`),
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`)
        REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `print_services` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(120) NOT NULL,
    `paper_size`     VARCHAR(10)  NOT NULL DEFAULT 'A4',
    `color_mode`     ENUM('bw','color') NOT NULL DEFAULT 'bw',
    `price_per_page` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `is_active`      TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `finishings` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`      VARCHAR(120) NOT NULL,
    `price`     DECIMAL(12,2) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `shifts` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED NOT NULL,
    `opened_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `closed_at`     DATETIME NULL,
    `opening_cash`  DECIMAL(14,2) NOT NULL DEFAULT 0,
    `closing_cash`  DECIMAL(14,2) NULL,
    `expected_cash` DECIMAL(14,2) NULL,
    `difference`    DECIMAL(14,2) NULL,
    `sales_total`   DECIMAL(14,2) NOT NULL DEFAULT 0,
    `expense_total` DECIMAL(14,2) NOT NULL DEFAULT 0,
    `note`          VARCHAR(255) NULL,
    `status`        ENUM('open','closed') NOT NULL DEFAULT 'open',
    PRIMARY KEY (`id`),
    KEY `idx_shifts_status` (`status`),
    CONSTRAINT `fk_shifts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `sales` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `invoice_no`      VARCHAR(40) NOT NULL,
    `shift_id`        INT UNSIGNED NULL,
    `user_id`         INT UNSIGNED NOT NULL,
    `customer_name`   VARCHAR(100) NULL,
    `subtotal`        DECIMAL(14,2) NOT NULL DEFAULT 0,
    `discount_amount` DECIMAL(14,2) NOT NULL DEFAULT 0,
    `tax_amount`      DECIMAL(14,2) NOT NULL DEFAULT 0,
    `total`           DECIMAL(14,2) NOT NULL DEFAULT 0,
    `paid`            DECIMAL(14,2) NOT NULL DEFAULT 0,
    `change_due`      DECIMAL(14,2) NOT NULL DEFAULT 0,
    `payment_method`  ENUM('cash') NOT NULL DEFAULT 'cash',
    `status`          ENUM('paid','void') NOT NULL DEFAULT 'paid',
    `note`            VARCHAR(255) NULL,
    `void_reason`     VARCHAR(255) NULL,
    `voided_at`       DATETIME NULL,
    `created_at`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_sales_invoice` (`invoice_no`),
    KEY `idx_sales_created` (`created_at`),
    KEY `idx_sales_status` (`status`),
    KEY `idx_sales_shift` (`shift_id`),
    CONSTRAINT `fk_sales_shift` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_sales_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `sale_items` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sale_id`    INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NULL,
    `item_type`  ENUM('product','print') NOT NULL DEFAULT 'product',
    `name`       VARCHAR(190) NOT NULL,
    `qty`        DECIMAL(12,2) NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `cost_price` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `subtotal`   DECIMAL(14,2) NOT NULL DEFAULT 0,
    `meta_json`  TEXT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_items_sale` (`sale_id`),
    KEY `idx_items_product` (`product_id`),
    CONSTRAINT `fk_items_sale` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `stock_movements` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id`   INT UNSIGNED NOT NULL,
    `type`         ENUM('in','out','adjust') NOT NULL,
    `qty`          INT NOT NULL,
    `stock_before` INT NOT NULL DEFAULT 0,
    `stock_after`  INT NOT NULL DEFAULT 0,
    `reference`    VARCHAR(60) NULL,
    `note`         VARCHAR(190) NULL,
    `user_id`      INT UNSIGNED NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_movements_product` (`product_id`),
    CONSTRAINT `fk_movements_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
CREATE TABLE `expenses` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `expense_date` DATE NOT NULL,
    `category`     VARCHAR(60) NOT NULL DEFAULT 'Operasional',
    `description`  VARCHAR(190) NOT NULL,
    `amount`       DECIMAL(14,2) NOT NULL DEFAULT 0,
    `user_id`      INT UNSIGNED NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_expenses_date` (`expense_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
