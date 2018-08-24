<?php

// Общие настройки
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//
//function debug($argument)
//{
//    echo "<pre>";
//    var_dump($argument);
//    echo "</pre>";
//    die;
//}

// стартуем сессию
session_start();

define('ROOT', dirname(__FILE__));
define('MAIN', ROOT . '\main');
define('CONFIG', ROOT . '\config');

// Подключаем автозагрузку классов
spl_autoload_register(function ($class_name) {

    // Массив папок, в которых могут находиться необходимые классы
    $array_paths = [
        '/component/',
        '/controller/',
        '/model/',
    ];

    // Проходим по массиву папок
    foreach ($array_paths as $path) {

        // Формируем имя и путь к файлу с классом
        $path = MAIN . $path . $class_name . '.php';

        // Если такой файл существует, подключаем его
        if (is_file($path)) {
            include_once $path;
        }
    }
});

// Вызов маршрутизатора
$router = new Router();
$router->run();