<?php

spl_autoload_register(function ($class) {
    $baseDir = dirname(__DIR__, 2) . '/';
    $file    = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});