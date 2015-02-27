# Разговорный бот на PHP для Вконтакте
Установка:
* 1. Импортируйте dump.sql в свою базу данных
* 2. Получите "долгоиграющий" access token для работы с VK API с помощью ссылки - [получить token](https://oauth.vk.com/authorize?client_id=4798482&redirect_uri=http://api.vk.com/blank.html&scope=offline,messages,friends,status&display=page&response_type=token)
* 3. Переименуйте config.sample.php в config.php предварительно изменив данные для коннекта к базе данных
* 4. Пользуйтесь запустив index.php