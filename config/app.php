<?php

/**
 * App Configuration
 */

// Website URL
define('URL', 'http://travian.dev');
// App root
define('RUT', $_SERVER['DOCUMENT_ROOT']);

// Set timezone
date_default_timezone_set('America/Sao_Paulo');

error_reporting(E_ALL); //^ E_NOTICE
ini_set("display_errors", true);
ini_set("log_errors", true);
ini_set("error_log", RUT . "/logs/php-error.txt");

// TRANSLATION
define('DEFAULT_LANGUAGE', 'en');
