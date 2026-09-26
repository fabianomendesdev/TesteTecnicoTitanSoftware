<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require(dirname(__FILE__, 2). '/app/Core/autoload.php');
require(dirname(__FILE__, 2). '/app/Configs/config.php');

use app\Core\Router;

try {
    $router = Router::getInstance();
    require(dirname(__FILE__, 2). '/routes/web.php');
    $router->process();
} catch (Throwable $th){
    exit("Aconteceu um erro no sistema! ". $th->getMessage());
}