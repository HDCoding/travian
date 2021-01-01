<?php

/**
 * App Configuration
 */

// Website URL
define('URL', 'http://travian.dev');
// App root
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('LOG_PATH', DOC_ROOT . '/logs/');

// Set timezone
date_default_timezone_set('America/Sao_Paulo');

error_reporting(E_ALL); //^ E_NOTICE
ini_set("display_errors", true);
ini_set("log_errors", true);
ini_set("error_log", LOG_PATH . "php-error.txt");

// TRANSLATION
define('DEFAULT_LANGUAGE', 'en');
