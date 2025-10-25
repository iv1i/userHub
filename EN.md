<p align="center"><img src="src/resources/docs/icons/people.png" width="300" alt="Laravel Logo"></p><a id='links'></a>

# <p align="center">UserHub</p>

### <p align="center">Web interface for the administration of records in the database</p>

### <p align="center">[Russian](README.md)</p>

## <img src="src/resources/docs/icons/link.png" width="35" align="absmiddle"> Links
### [Technologies](#technologies) | [Description](#description) | [Routes](#routes) | [Init](#init) | [Interaction](#interaction) | [Gallery](#gallery)

## <img src="src/resources/docs/icons/tools.png" width="35" align="absmiddle"> Technologies <a id='technologies'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)

[PHP 8.4](https://www.php.net/) - Programming language.

[Mysql 8.0](https://www.mysql.com/) - Database.

[Docker](https://www.docker.com/) - Containerization.

HTML/CSS/JS - Frontend.


## <img src="src/resources/docs/icons/book2.png" width="35" align="absmiddle"> Description <a id='description'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)

The project is a pure PHP web service.
> [!WARNING]
> This service is written entirely from scratch, including MVC and routing.

### Basic entities

- **Users** (DB: users)
- **Admins** (DB: admins)

### Database dump:
- [dump.sql](src/database/dump.sql)

### Technical features
- **MVC**
- **Template Engine**
- **Pagination** from users
- **Sorting** from users
- **type hints**
- **Validation** via Requests
- **Business logic is represented in Services**
- **DTO** for data exchange between layers

## <img src="src/resources/docs/icons/route.png" width="35" align="absmiddle"> Routes <a id='routes'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)

#### Auth
| Method | Endpoint | Description     |
|-------|----------|-----------------|
| POST  | `/login` | Login for admin |
| GET  | `/login` | Page for login  |

#### Secure routes (authentication required)

| Method | Endpoint              | Description    |
|-------|-----------------------|----------------|
| GET   | `/users`              | get all users  |
| GET   | `/users/create`       | create user    |
| GET   | `/users/{id}`         | get user       |
| GET   | `/users//{id}/edit`   | Edit user      |
| GET   | `/logout`             | Exit for admin |
| GET   | `/users/{id}/delete`  | Delete user   |
| POST  | `/users/{id} `        | Update user    |
| POST   | `/users/create`      | Create user    |

## <img src="src/resources/docs/icons/rocket.png" width="30" align="absmiddle"> Init <a id='init'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)
> [!NOTE]
> It is recommended to use DNS 8.8.8.8 to avoid problems with containers.
### Setting up the environment
```bash
cp .env.example .env
```
### Start Docker
```bash
docker compose up -d --build
```
### After successful launch, the service will be available at:
### `http://localhost`

## <img src="src/resources/docs/icons/fire.png" width="35" align="absmiddle"> Interaction <a id='interaction'></a>[<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)
### Administrator login details:
```
username: admin, 
password: password
```
### Sorting:
```
?page=1&sort_by=id&sort_order=ASC
?page=1&sort_by=id&sort_order=DESC
```
### Fields for sorting:
```
id, username, first_name, last_name
```

## <img src="src/resources/docs/icons/flower.png" width="30" align="absmiddle"> <a id='gallery'></a> Gallery [<img src="src/resources/docs/icons/up.png" width="20" align="absmiddle">](#links)

<img src="src/resources/docs/UI/1.png">
<img src="src/resources/docs/UI/2.png">
<img src="src/resources/docs/UI/3.png">
<img src="src/resources/docs/UI/4.png">
<img src="src/resources/docs/UI/5.png">
