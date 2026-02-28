-- Таблица компаний
CREATE TABLE IF NOT EXISTS `#__crm_companies` (
                                                  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT '',
    `current_stage` varchar(50) NOT NULL DEFAULT 'Ice',
    `discovery_data` text DEFAULT NULL,
    `created` datetime NOT NULL,
    `modified` datetime NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Таблица событий (история)
CREATE TABLE IF NOT EXISTS `#__crm_events` (
                                               `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `company_id` int(11) UNSIGNED NOT NULL,
    `event_code` varchar(50) NOT NULL,
    `comment` text,
    `created` datetime NOT NULL,
    INDEX `idx_company` (`company_id`),
    INDEX `idx_created` (`created`),
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Добавим тестовую компанию
INSERT INTO `#__crm_companies` (`name`, `current_stage`, `created`, `modified`)
VALUES ('Test Company LLC', 'Ice', NOW(), NOW());