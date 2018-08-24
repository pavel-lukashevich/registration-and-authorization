<?php

class Cookie
{
    // условно месяц для жизни куки
    const MONTH = 60 * 60 * 24 * 30;

    /**
     * создаём куки на месяц
     * @param Users $users
     * @param $numberUser integer
     */
    public static function create(Users $users, $numberUser)
    {
        // создаем куки и пишем в базу
        $cookieKey = $users::generateSalt(20);
        $user = $users->addCookieKey($numberUser, $cookieKey);
        // создаём куки на месяц
        setcookie("login", (string)$user->user[$numberUser]->login, time() + self::MONTH, '/');
        setcookie("cookieKey", $cookieKey, time() + self::MONTH, '/');
        header("Refresh:0");
    }

    /**
     * обнуляем куки
     */
    public static function destroy()
    {
        // сломать куки
        setcookie("login", '', time(), '/');
        setcookie("cookieKey", '', time(), '/');
    }
}