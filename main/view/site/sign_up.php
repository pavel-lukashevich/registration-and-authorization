<form onsubmit="return false;" id="sign_up_form">
    <h3>Регистрация</h3>
    <div class="form-group">
        <input type="text" class="form-control" name="signUpForm[login]" placeholder="введите ваш логин">
    </div>
    <div class="form-group">
        <input type="password" class="form-control" name="signUpForm[password]" placeholder="введите ваш пароль">
    </div>
    <div class="form-group">
        <input type="password" class="form-control" name="signUpForm[confirm_password]"
               placeholder="повторите ваш пароль">
    </div>
    <div class="form-group">
        <input type="email" class="form-control" name="signUpForm[email]" placeholder="введите ваш email">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" name="signUpForm[name]" placeholder="введите ваше имя">
    </div>
    <div class="alert alert-danger  display-none" id="sign-up-errors">
    </div>
    <div class="alert alert-success  display-none" id="sign-up-success">
    </div>

    <button type="button" class="btn btn-block btn-primary" id="sign-up-button">зарегистрироваться</button>
</form>

