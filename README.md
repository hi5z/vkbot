# Разговорный бот на PHP для Вконтакте
##Установка:
![ID бота](http://i2.wp.com/i.gyazo.com/7cd78815452ddf729e14815e4c0efb72.png?resize=625%2C43)
* 1. Зарегистрируйтесь на сайте iii.ru и создайте себе "инфа". Используйте его номер в скрипте.
* 2. Импортируйте `DATABASE.sql` в свою базу данных
* 3. Получите "долгоиграющий" access token для работы с `VK API` с помощью ссылки - [получить token](https://oauth.vk.com/authorize?client_id=4798482&redirect_uri=http://api.vk.com/blank.html&scope=offline,messages,friends,status,wall&display=page&response_type=token). Естественно вы можете отредактировать ссылку и использовать свои данные изменив токен на временный.
* 4. Переименуйте `config.sample.php` в `config.php` предварительно изменив данные для коннекта к базе данных
* 5. Пользуйтесь запустив `index.php`

![Приблизительно так должен выглядеть ваш `config.php`](http://i.mew.su/p/o/1432635581.png)