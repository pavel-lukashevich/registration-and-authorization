<?php

class Session
{
    /**
     * создаём сессию
     * @param Users $users
     * @param $number integer
     */
    public static function create(Users $users, $number)
    {
        // создаем сессию и пишем в базу
        $sessionKey = Users::generateSalt(20);
        $user = $users->addSessionKey($number, $sessionKey);

        //Пишем в сессию информацию о том, что мы авторизовались:
        $_SESSION['auth'] = true;
        $_SESSION['login'] = (string)$user->user[$number]->login;
        $_SESSION['name'] = (string)$user->user[$number]->name;
        $_SESSION['sessionKey'] = (string)$user->user[$number]->session_key;
    }

    /**
     * удаляем данные из сессии
     */
    public static function delete()
    {
        // сломать сессию и куки
        unset($_SESSION['auth']);
        unset($_SESSION['login']);
        unset($_SESSION['name']);
        unset($_SESSION['sessionKey']);
        session_destroy();
    }

}