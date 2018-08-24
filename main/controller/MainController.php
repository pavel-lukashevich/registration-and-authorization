<?php

/**
 * родительский класс для контроллеров
 * содержит общие методы
 */
class MainController
{
    /**
     * проверка есть ли сессия
     * @return bool
     */
    public static function existSession()
    {
        if (isset($_SESSION['auth']) && $_SESSION['auth']) {
            // сравниваем сессионный ключ и ключ в БД
            $users = new Users();
            if ($users->equalitySessionKey($_SESSION['login'], $_SESSION['sessionKey'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * проверяем сессию, если есть то не гость,
     * если сессии нет, то смотрим куки
     * если куки есть, создаём сессию,
     * если нет, значит он гость
     * @return bool
     */
    public static function isGuest()
    {
        //проверяем сессию, если ее нет, проверяем куки
        if (!self::existSession()) {
            // если куки есть и он не пустой
            if (isset($_COOKIE['login']) && $_COOKIE['login'] != '') {
                $users = new Users();
                // если куки-ключ совпадает с ключем в БД
                if ($users->equalityCookieKey($_COOKIE['login'], $_COOKIE['cookieKey'])) {
                    // делаем новую сессию
                    $numberUser = (int)$users->searchObjectNumberByLogin($_COOKIE['login']);
                    Session::create($users, $numberUser);
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * выводим json для ajax запросов
     * @param array $param
     */
    public static function responseJson($param = [])
    {
        header('Content-type: application/json');
        echo json_encode($param);
        exit;
    }


    /**
     * подгружает представление страница
     * @param $view string
     * @return bool
     */
    public function render($view)
    {
        $view = trim($view, '/');
        include MAIN . '/view/header.php';
        include MAIN . '/view/' . $view . '.php';
        include MAIN . '/view/footer.php';

        return true;
    }

    /**
     * перенаправляет на страницу, если пусто - на главную
     * @param $view string
     */
    public function redirect($view)
    {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri/$view");
        die;
    }
}