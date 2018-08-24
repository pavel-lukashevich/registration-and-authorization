$(document).ready(function () {

    // обработка кнопки авторизации
    $('#sign-in-button').click(function () {
        $.ajax({
            type: 'POST',
            url: '/site/signin',
            dataType: 'json',
            data: $('#sign_in_form').serialize(),
            success: function (data) {
                if (data.success) {
                    //перезагрузка страницы
                    location.reload();
                } else {
                    // выводим ошибку
                    $('#sign-in-errors').html(data.message).show();
                }
                //скрываем предупреждения формы регистрации
                $('#sign-up-errors').html('').hide();
                $('#sign-up-success').html('').hide();
                // очищаем форму регистрации
                $('#sign_up_form')[0].reset();
            }
        });
    });

    // обработка кнопки регистрации
    $('#sign-up-button').click(function () {
        $.ajax({
            type: 'POST',
            url: '/site/signup',
            dataType: 'json',
            data: $('#sign_up_form').serialize(),
            success: function (data) {
                if (data.success) {
                    // поздравляем с регистрацией.
                    $('#sign-up-errors').html(data.message).hide();
                    $('#sign-up-success').html(data.message).show();
                    // очищаем форму
                    $('#sign_up_form')[0].reset();
                } else {
                    // выводим ошибку
                    $('#sign-up-errors').html(data.message).show();
                    $('#sign-up-success').html(data.message).hide();
                }
                //скрываем предупреждения формы авторизации
                $('#sign-in-errors').html('').hide();
                // очищаем форму авторизации
                $('#sign_in_form')[0].reset();
            }
        });
    });

});