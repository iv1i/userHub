<p align="center"><img src="src/resources/docs/icons/people.png" width="300" alt="Laravel Logo"></p><a id='links'></a>

# <p align="center">UserHub</p>

### <p align="center">Web-интерфейс для администрирования записей в БД</p>
### <p align="center">[English](EN.md)</p>

## <img src="src/resources/docs/icons/link.png" width="35" align="absmiddle"> Ссылки
### [Технологии](#technologies) | [Описание](#description) | [Маршруты](#routes) | [Инициализация](#init) | [Взаимодействие](#interaction) | [Галерея](#gallery)
## <img src="src/resources/docs/icons/tools.png" width="35" align="absmiddle"> Используемые технологии <a id='technologies'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)

[PHP 8.4](https://www.php.net/) - Язык программироваия.

[Mysql 8.0](https://www.mysql.com/) - База данных.

[Docker](https://www.docker.com/) - Контейнеризация.

HTML/CSS/JS - Фронтенд.


## <img src="src/resources/docs/icons/book2.png" width="35" align="absmiddle"> Описание <a id='description'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)

Проект представляет собой чистый PHP-веб сервис.
> [!WARNING]
> Данный сервис написан полностью с нуля, включая MVC и маршрутизацию.

### Основные сущности

- **Пользователи** (DB: users)
- **Администраторы** (DB: admins)

### Дамп БД
- [dump.sql](src/database/dump.sql)

### Технические особенности
- **MVC**
- **Шаблонизатор**
- **Пагинация** для пользователей
- **Cортировка**
- **type hints**
- **Валидация** через Requests
- **Бизнес логика представлена в Services**
- **DTO** для обмена данными между слоями
## <img src="src/resources/docs/icons/route.png" width="35" align="absmiddle"> Маршруты <a id='routes'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)

#### Аутентификация
| Метод | Эндпоинт               | Описание          |
|-------|------------------------|-------------------|
| POST  | `/login`    | Вход для админа   |
| GET  | `/login` | Вход для админа   |


#### Защищенные маршруты (требуется аутентификация)

| Метод | Эндпоинт              | Описание                             |
|-------|-----------------------|--------------------------------------|
| GET   | `/users`              | Получить пользователей(с пагинацией) |
| GET   | `/users/create`       | Создать пользователя                 |
| GET   | `/users/{id}`         | Получить пользователя                |
| GET   | `/users//{id}/edit`   | Редактировать пользователя           |
| GET   | `/logout`             | Выход для админа                     |
| GET   | `/users/{id}/delete`  | Удалить пользователя                 |
| POST  | `/users/{id} `        | Обновить пользователя                |
| POST   | `/users/create`      | Создать пользователя                 |

## <img src="src/resources/docs/icons/rocket.png" width="30" align="absmiddle"> Инициализация <a id='init'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)
> [!NOTE]
> Рекомендуется использовать DNS 8.8.8.8 для избежания проблем с контейнерами.
### Настройка окружения
```bash
cp .env.example .env
```
### Запуск Docker
```bash
docker compose up -d --build
```
### После успешного запуска система будет доступна по адресу: 
### `http://localhost`

## <img src="src/resources/docs/icons/fire.png" width="35" align="absmiddle"> Взаимодействие с системой  <a id='interaction'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)
### Данные для входа администратора:
```
username: admin, 
password: password
```
### Сортировка:
```
?page=1&sort_by=id&sort_order=ASC
?page=1&sort_by=id&sort_order=DESC
```
### Поля для сортировки:
```
id, username, first_name, last_name
```
## <img src="src/resources/docs/icons/flower.png" width="30" align="absmiddle"> <a id='gallery'></a> Галерея [<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)

<img src="src/resources/docs/UI/1.png">
<img src="src/resources/docs/UI/2.png">
<img src="src/resources/docs/UI/3.png">
<img src="src/resources/docs/UI/4.png">
<img src="src/resources/docs/UI/5.png">
