<?php

/**
 * Класс Router
 * Компонент для работы с маршрутами
 */
class Router
{

    // Массив с внешними и внутренними путями
    private $routes;

    public function __construct()
    {
        // Получаем маршруты из файла
        $this->routes = include(CONFIG . '\routes.php');
    }

    /**
     * Метод для обработки запроса
     */
    public function run()
    {
        // Получаем строку запроса
        $uri = trim($_SERVER['REQUEST_URI'], '/');

        // Проверяем наличие запроса в массиве маршрутов
        foreach ($this->routes as $uriPattern => $path) {

            // Проверяем содержит ли запрос запланированные пути  
            if (preg_match("~$uriPattern~", $uri)) {

                // Получаем внутренний путь из внешнего согласно правилу
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri, 1);

                // Определяем контроллер и метод
                $segments = explode('/', $internalRoute);
                $controllerName = ucfirst(array_shift($segments)) . 'Controller';
                $actionName = 'action' . ucfirst(array_shift($segments));

                // Создать объект контроллера и вызываем метод
                $controllerObject = new $controllerName;
                $result = $controllerObject->$actionName();

                // Если метод контроллера успешно вызван, завершаем работу роутера
                if ($result != null) {
                    break;
                }
            }
        }
    }

}
