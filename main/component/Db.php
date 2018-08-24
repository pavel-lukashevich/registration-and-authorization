<?php

class Db
{

    public static function getTablePatch()
    {
        // Получаем параметры подключения из файла
        $paramsPath = ROOT . '/config/db.php';
        return include($paramsPath);
    }

    public static function getUsersTable()
    {
        // Устанавливаем соединение
        $db = simplexml_load_file(self::getTablePatch()['Users']);

        return $db;
    }

}