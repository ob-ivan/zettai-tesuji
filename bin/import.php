<?php
/**
 * Скрипт для запуска из командной строки, импортирует задачи из треда в базу.
**/

// Пути.
define ('DOCUMENT_ROOT', dirname(__DIR__));
define ('AUTOLOAD_PATH', DOCUMENT_ROOT . '/vendor/autoload.php');

// Зависимости.
require_once AUTOLOAD_PATH;

