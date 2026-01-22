<?php
/**
 * ОБРАЗЕЦ конфигурации SMTP для отправки почты через Яндекс
 * 
 * ЭТО ПРИМЕР! Скопируйте этот файл в smtp_config.php и заполните реальные данные
 * 
 * Инструкция:
 * 1. Скопируйте этот файл: cp smtp_config.example.php smtp_config.php
 * 2. Откройте smtp_config.php
 * 3. Заполните реальные данные (email и пароль)
 * 4. Никогда не заполняйте реальные данные в THIS файле!
 */

// Запрет прямого доступа к файлу
if (!defined('SMTP_CONFIG_LOADED')) {
    die('Прямой доступ запрещён');
}

// ============================================
// НАСТРОЙКИ SMTP
// ============================================

define('SMTP_HOST', 'smtp.yandex.ru');
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');
define('SMTP_AUTH', true);

// ⚠️ ЗАПОЛНИТЕ В smtp_config.php (НЕ В ЭТОМ ФАЙЛЕ!)
define('SMTP_USERNAME', 'ваш_email@yandex.ru');
define('SMTP_PASSWORD', 'ваш_пароль_приложения');

define('SMTP_FROM_EMAIL', 'ваш_email@yandex.ru');
define('SMTP_FROM_NAME', 'KROM Marketing');

define('MAIL_TO', 'krommarketing@ya.ru');

