<?php
   define('CONFIRMATION_TERM', 1800); //86400
   define('COOKIE_LIFETIME', 86400000);
   define('ERROR_QUERY', 'В данный момент невозможно подключение к базе данных.');
   define('ERROR_MAIL', 'Подтверждение e-mail невозможно.');
   define('ERROR_MAIL_ALREADY_REGISTERED', 'Этот e-mail уже зарегистрирован, используйте другой.');
   define('ERROR_MAIL_CONFIRM', 'Не истекло время подтверждения e-mail.');
   define('ERROR_MAIL_CONFIRM_EXPIRED', 'Истекло время подтверждения e-mail.');
   define('ERROR_LOGIN', 'Неверное имя пользователя или пароль.');
   define('ERROR_CAPTCHA', 'Ошибка при вводе символов с картинки');
   define('NUMBER_OF_LOGIN_ATTEMPTS', 3);
?>