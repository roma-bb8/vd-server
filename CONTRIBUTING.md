# Запустить проект

### 1. Создать конфигурационнй файлы в `/app/Config/local.json`.

#### Пример файла:
```
{
  "debug": true,
  "locale": "ru",
  "jwt": {
    "secret.key": "secret",
    "life.time": 2880,
    "algorithm": [
      "HS256"
    ],
    "options": {
      "username.claim": "phone",
      "header.name": "Authorization",
      "token.prefix": "Bearer"
    }
  },
  "dbs.options": {
    "local": {
      "driver": "pdo_mysql",
      "host": "127.0.0.1",
      "dbname": " %DB_NAME% ",
      "user": " %USER% ",
      "password": " %PASSWORD% ",
      "charset": "utf8"
    }
  },
  "swift.mailer": {
    "host": "smtp.gmail.com",
    "port": 587,
    "username": " %EMAIL% ",
    "password": " %PASSWORD% ",
    "encryption": "tls",
    "auth.mode": "login"
  }
}
```

### 2. Выполнить команды:

1. `composer install` подтянуть зависимости
2. `php ./bin/db/create.php` создать схему базы данных
3. `php ./bin/db/root.php` создать базу и добавить суперпользователя
4. `php ./bin/db/data.php` заполнить базу данных
5. `composer app-run` запустить сервер

# Тест материалы:

> В директории `/tmp/postman` находятся два json файла
* `vd.postman_environment` окружение
* `vd.postman_collection` коллекция urls

# Доп. материалы:

> В директории `/tmp/db` находятся три sql файла
* `scheme.sql` все таблицы и связи базы данных
* `root.sql` добавляет в базу данных суперпользователя (root)
* `data.sql` содержит в себе тестовые записи
