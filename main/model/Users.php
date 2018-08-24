<?php

class Users
{
    // объект базы данных
    public $dbUsers;

    //  массив собирающий ошибки валидации
    public $errorsValidate = null;

    /**
     * присваиваем значения объект БД
     * Users constructor.
     */
    public function __construct()
    {
        $this->dbUsers = Db::getUsersTable();
    }

    /**
     * валидация данных и регистраци пользователя
     * @param array $signUpForm
     * @return bool|array
     */
    public function signUpUser($signUpForm)
    {
        // валидация принятых данных
        $this->validateForSignUp($signUpForm);

        // если есть ошибки валидации, передаем массив ошибок
        // если ошибок нет то добавляем поля в объект БД и сохраняем файл
        if ($this->errorsValidate !== null) {
            return $this->errorsValidate;
        } else {
            $newUser = $this->dbUsers->addChild('user');
            $newUser->addChild('login', $signUpForm['login']);
            $newUser->addChild('email', $signUpForm['email']);
            $newUser->addChild('name', $signUpForm['name']);
            $newUser->addChild('salt', self::generateSalt());
            $newUser->addChild('password_hash', $this->makeSaltyPassword($signUpForm['password'], $newUser->salt));

            // сохраняем нового юзера
            $this->dbUsers->asXML(Db::getTablePatch()['Users']);
            return true;
        }
    }

    /**
     * валидация данных и авторизация пользователя
     * @param array $signInForm
     * @return bool|array
     */
    public function signInUser($signInForm)
    {
        // валидация принятых данных
        $this->validateForSignIn($signInForm);
        // если есть ошибоки валидации, передаем массив ошибок
        if ($this->errorsValidate === null) {
            return true;
        } else {
            return $this->errorsValidate;
        }
    }


    /**
     * валидация полей и проверка пароля для авторизации
     * @param array $signInForm
     */
    public function validateForSignIn(array $signInForm)
    {
        // проверка на длину и пустоту
        $signInForm = $this->validateNotNull($signInForm);
        //меняем спецсимволы на html сущности
        $signInForm = $this->screening($signInForm);
        // ищем пользователя в базе и проверяем совпадение паролей
        $user = $this->searchByLogin($signInForm['login']);
        if ($user === false || !$this->equalityPassword($user, $signInForm['password'])) {
            $this->errorsValidate[] = 'ошибка в логине или пароле';
        }
    }

    /**
     * валидация полей и проверка пароля для регистрации
     * @param array $signUpForm
     */
    public function validateForSignUp($signUpForm)
    {
        // проверка на длину и пустоту
        $signUpForm = $this->validateNotNull($signUpForm);
        // проверяем формат email
        $this->validateEmale($signUpForm['email']);
        //меняем спецсимволы на html сущности
        $signUpForm = $this->screening($signUpForm);
        // сравниваем пароль и подтверждение
        $this->validatePassword($signUpForm['password'], $signUpForm['confirm_password']);
        // проверяем уникальность логина
        $this->validateUniqueLogin($signUpForm['login']);
        // проверяем уникальность емайл
        $this->validateUniqueEmail($signUpForm['email']);
    }

    /**
     * удаление пробелов вначале и вконце, проверка поля на длину и пустоту
     * @param array $param
     * @return array
     */
    public function validateNotNull(array $param)
    {
        $newParam = [];
        $nullFlaf = false;
        foreach ($param as $key => $value) {
            $value = trim($value);
            if (strlen($value) == 0) {
                $nullFlaf = true;
            } elseif (strlen($value) < 2) {
                $this->errorsValidate[] = 'поле "' . $key . '" не должно быть короче двух символов';
            } elseif (strlen($value) > 50) {
                $this->errorsValidate[] = 'поле "' . $key . '" не должно быть длинее 50 символов';
            }
            $newParam[$key] = $value;
        }

        if ($nullFlaf) {
            $this->errorsValidate[] = 'пожалуйста, заполните все поля';
        }
        return $newParam;
    }

    /**
     * заменяем спецсимволы на сущности
     * @param array $param
     * @return array
     */
    public function screening(array $param)
    {
        $newParam = [];
        foreach ($param as $key => $value) {
            $newParam[$key] = htmlspecialchars($value);
        }
        return $newParam;
    }

    /**
     * проверяем формат записи email
     * @param string $email
     * @return bool
     */
    public function validateEmale($email)
    {
        if (preg_match('/.+@.+\..+/i', $email) == 0) {
            $this->errorsValidate[] = '"' . $email . '" не соответствует формату email';
            return false;
        }
        return true;
    }

    /**
     * сравниваем пароль и подтверждение
     * @param string $password
     * @param string $confirm_password
     * @return bool
     */
    public function validatePassword($password, $confirm_password)
    {
        if ($password !== $confirm_password) {
            $this->errorsValidate[] = 'пароль и подтверждение не совпадают';
            return false;
        }
        return true;
    }

    /**
     * проверяем соответствие введенного пароля соленому хэшу в базе
     * @param object $user
     * @param string $password
     * @return bool
     */
    public function equalityPassword($user, $password)
    {
        $saltyPassword = $this->makeSaltyPassword($password, $user->salt);
        if ($saltyPassword == $user->password_hash) {
            return true;
        }
        return false;
    }

    /**
     * солим пароль и возвращаем хэш
     * @param string $password
     * @param string $salt
     * @return string
     */
    public function makeSaltyPassword($password, $salt)
    {
        return md5($salt . md5($password));
    }

    /**
     * получаем соль для пароля, cookie_key и session_key
     * генерирует хэш строку до 32 символов из рандомного числа, по умолчанию 10 символов
     * @param int $lehgth
     * @return bool|string
     */
    public static function generateSalt($lehgth = 10)
    {
        return substr(md5(mt_rand()), 0, $lehgth);
    }

    /**
     * проверяет уникальность логина
     * @param string $login
     * @return bool
     */
    public function validateUniqueLogin($login)
    {
        $user = $this->searchByLogin($login);
        if ($user !== false) {
            $this->errorsValidate[] = 'пользователь с логином "' . $login . '"" уже есть';
            return false;
        }
        return true;
    }

    /**
     * ищет по логину и возвращает объект пользователя или false из массива объектов базы
     * @param string $login
     * @return bool|object
     */
    public function searchByLogin($login)
    {
        $resultObject = false;
        foreach ($this->dbUsers as $value) {
            if (htmlspecialchars_decode(trim($login)) == trim($value->login)) {
                $resultObject = $value;
                break;
            }
        }
        return $resultObject;
    }

    /**
     * возвращает номер объекта в массиве объектов БД
     * @param string $login
     * @return bool|int
     */
    public function searchObjectNumberByLogin($login)
    {
        $objectNumber = false;
        $users = (array)$this->dbUsers;

        for ($i = 0; $i <= count($users['user']); $i++) {
            if ($login == (string)$users['user'][$i]->login) {
                $objectNumber = $i;
                break;
            }
        }
        return $objectNumber;
    }

    /**
     * проверяем email на уникальность
     * @param string $email
     * @return bool
     */
    public function validateUniqueEmail($email)
    {
        $user = $this->searchByEmail($email);
        if ($user !== false) {
            $this->errorsValidate[] = 'пользователь с таким email уже есть';
            return false;
        }
        return true;
    }

    /**
     * ищет по email и возвращает объект пользователя или false из массива объектов базы
     * @param string $email
     * @return bool|object
     */
    public function searchByEmail($email)
    {
        $resultObject = false;
        foreach ($this->dbUsers as $value) {
            if (trim($email) == trim($value->email)) {
                $resultObject = $value;
                break;
            }
        }
        return $resultObject;
    }

    /**
     * добавляет пользователю ключ сессии и сохраняет в БД
     * @param integer $number
     * @param string $sessionKey
     * @return SimpleXMLElement
     */
    public function addSessionKey($number, $sessionKey)
    {
        $this->dbUsers->user[$number]->session_key = $sessionKey;
        $this->dbUsers->asXML(Db::getTablePatch()['Users']);
        return $this->dbUsers;
    }

    /**
     * сравнивает сессионный-ключ пользователя и ключ в БД
     * @param string $login
     * @param string $sessioKey
     * @return bool
     */
    public function equalitySessionKey($login, $sessioKey)
    {
        $user = $this->searchByLogin($login);
        if ($user->session_key == $sessioKey) {
            return true;
        }
        return false;
    }

    /**
     * добавляет пользователю ключ куки и сохраняет в БД
     * @param integer $number
     * @param string $cookieKey
     * @return SimpleXMLElement
     */
    public function addCookieKey($number, $cookieKey)
    {
        $this->dbUsers->user[$number]->cookie_key = $cookieKey;
        $this->dbUsers->asXML(Db::getTablePatch()['Users']);
        return $this->dbUsers;
    }

    /**
     * сравнивает куки-ключ пользователя и ключ в БД
     * @param string $login
     * @param string $cookieKey
     * @return bool
     */
    public function equalityCookieKey($login, $cookieKey)
    {
        $user = $this->searchByLogin($login);
        if ($user->cookie_key == $cookieKey) {
            return true;
        }
        return false;
    }


}