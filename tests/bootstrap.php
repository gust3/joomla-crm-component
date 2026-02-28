<?php
/**
 * Bootstrap для Unit-тестов (без зависимости от Joomla)
 */

// Автозагрузка Composer
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// 🔹 Загружаем Mock-классы ДО регистрации алиаса
require_once __DIR__ . '/Mocks/Factory.php';

// 🔹 Регистрируем алиас Factory ПОСЛЕ загрузки Mock-класса
if (!class_exists('Joomla\\CMS\\Factory', false)) {
    class_alias('Gust\\Component\\Crmstages\\Tests\\Mocks\\Factory', 'Joomla\\CMS\\Factory');
}