<?php
date_default_timezone_set('America/Recife');
setlocale(LC_TIME, 'pt_BR', 'pt-BR.utf-8', 'portuguese');

//env
$_ENV=parse_ini_file(realpath(dirname(__FILE__) . '/../../.env'));

// Pastas
define('VIEW_PATH', realpath(dirname(__FILE__) . '/../Views'));
define('MODEL_PATH', realpath(dirname(__FILE__) . '/../Models'));
define('ASSETS_PATH', realpath(dirname(__FILE__) . '/../../public/assets'));

// Arquivos
require_once(realpath(dirname(__FILE__))."/../Helpers/functions.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);