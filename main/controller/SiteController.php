<?php

class SiteController extends MainController
{
    /**
     *  титульная страница.
     *  проверяет авторизацию, и формирует представление
     *  либо формы авторизации и регистрации, либо страницу приветствия
     * @return bool
     */
    public function actionIndex()
    {
        //если гость то показываем формы
        if (self::isGuest()) {
            return $this->render('site/index');
        }

        // если авторизован, титульная страница
        return $this->render('site/hello');
    }

    /**
     * страница приветствия
     * @return bool
     */
    public function actionHello()
    {
        return $this->render('site/hello');
    }

    /**
     * авторизаци, метод вызывается через ajax
     * возвращает json
     */
    public function actionSignin()
    {
        $users = new Users();

        // валидация полей и проверка соответствия пароля
        $activateUser = $users->signInUser($_POST['signInForm']);
        if ($activateUser === true) {

            // получаем номер пользователя в массиве
            $numberUser = (int)$users->searchObjectNumberByLogin($_POST['signInForm']['login']);

            // если чекбокс есть, то создаём куки, если нет то только сессию
            if (isset($_POST['check'])) {
                Cookie::create($users, $numberUser);
            }

            // создаем сессию и пишем в базу
            Session::create($users, $numberUser);

            self::responseJson([
                'success' => true,
                'message' => 'добро пожаловать',
            ]);
        } else {
            self::responseJson([
                'success' => false,
                'message' => implode('<br>', $activateUser),
            ]);
        }
    }

    /**
     * регистрация, метод вызывается через ajax
     * возвращает json
     */
    public function actionSignup()
    {
        $users = new Users();
        $registerUser = $users->signUpUser($_POST['signUpForm']);
        if ($registerUser === true) {
            self::responseJson([
                'success' => true,
                'message' => 'спасибо за регистрацию, теперь вы можете авторизоваться',
            ]);
        } else {
            self::responseJson([
                'success' => false,
                'message' => implode('<br>', $registerUser),
            ]);
        }
    }

    /**
     * ломаем куки и сессию и переходим на титульную страницу
     */
    public function actionLogout()
    {
        // сломать сессию
        Session::delete();

        // сломать куки
        Cookie::destroy();

        $this->redirect('');
    }

}