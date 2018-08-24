<?php

// Возвращаем массив 'внешний путь' => 'внутренний путь'
// Все страницы сайта необходимо вносить в массив
return array(
    'site/logout' => 'site/logout',
    'site/signin' => 'site/signin',
    'site/signup' => 'site/signup',
    'site/hello' => 'site/hello',
    'site/index' => 'site/index',
    'site' => 'site/index',
    '.*' => 'site/index',
);