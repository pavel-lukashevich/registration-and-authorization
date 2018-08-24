<form onsubmit="return false;" id="sign_in_form">
    <h3>Авторизация</h3>
    <div class="form-group">
        <input type="text" class="form-control" name="signInForm[login]" placeholder="введите ваш логин">
    </div>
    <div class="form-group">
        <input type="password" class="form-control" name="signInForm[password]" placeholder="введите ваш пароль">
    </div>

    <div class="alert alert-danger  display-none" id="sign-in-errors">
    </div>

    <div class="form-group">
        <input type="checkbox" class="form-check-input" name="check" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">запомнить</label>
    </div>

    <button type="button" class="btn btn-block btn-primary" id="sign-in-button">войти</button>
</form>

