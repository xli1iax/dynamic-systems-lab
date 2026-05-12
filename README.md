
## Slim backend setup

Project uses Slim Framework as a lightweight PHP backend framework.  
The application runs in Docker with Apache, PHP, MariaDB and phpMyAdmin.

### Project structure

```text
database/
  schema.sql         # database structure, CREATE TABLE commands
  seed.sql           # test data, INSERT commands

docker/
  apache.conf        # Apache configuration

nginx/
  default.conf       # old nginx config / optional config

php/
  Dockerfile         # PHP + Apache image configuration

public/
  index.php          # front controller, all routes start here
  .htaccess          # redirects requests to index.php
  css/
  js/

src/
  Controllers/       # request handlers
  Services/          # business logic, database, Octave, PDF, CSV, etc.
  Models/            # database models
  config.php         # application configuration and database connection

views/               # PHP templates
storage/logs/        # application logs
````

### Installed dependencies

```bash
composer require slim/slim slim/psr7 symfony/process monolog/monolog vlucas/phpdotenv
```

### Environment configuration

The real `.env` file is not committed to git.
Create it from the example file:

```bash
cp .env.example .env
```

Example variables:

```env
MYSQL_ROOT_PASSWORD=root_password
MYSQL_DATABASE=dynamic_system_db
MYSQL_USER=app_user
MYSQL_PASSWORD=app_password

DB_HOST=db
DB_PORT=3306
```

`MYSQL_*` variables are used by the MariaDB Docker container.
`DB_HOST` and `DB_PORT` are used by the PHP application.

Inside Docker, the database host is:

```text
db
```

not `localhost`.

### Run project with Docker

Start the project:

```bash
docker compose up -d --build
```

Or rebuild from zero without deleting the database volume:

```bash
docker compose down
docker compose build --no-cache
docker compose up -d
```

Application:

```text
http://localhost:8080/
```

phpMyAdmin:

```text
http://localhost:8081/
```

Test API endpoint:

```text
http://localhost:8080/api/test
```

### Database

MariaDB runs in a separate Docker container.

Database data is stored in a Docker volume:

```yaml
volumes:
  - db_data:/var/lib/mysql
```

This means the database is preserved after:

```bash
docker compose down
docker compose up
docker compose build
```

The database is deleted only if the volume is removed:

```bash
docker compose down -v
```

### Database schema and seed

Database structure should be stored in:

```text
database/schema.sql
```

Test data should be stored in:

```text
database/seed.sql
```

Example:

```text
schema.sql -> CREATE TABLE ...
seed.sql   -> INSERT INTO ...
```

These files should be committed to git, so every team member can recreate the same database structure and test data.

In `docker-compose.yml`, SQL files are mounted into MariaDB initialization folder:

```yaml
db:
  image: mariadb:11
  volumes:
    - db_data:/var/lib/mysql
    - ./database/schema.sql:/docker-entrypoint-initdb.d/01_schema.sql
    - ./database/seed.sql:/docker-entrypoint-initdb.d/02_seed.sql
```

These SQL files are executed automatically only when the database volume is created for the first time.

If `schema.sql` or `seed.sql` was changed and the local database must be recreated, run:

```bash
docker compose down -v
docker compose up -d --build
```

Warning: this deletes the local database data.

### phpMyAdmin login

Open:

```text
http://localhost:8081/
```

Use credentials from `.env`:

```text
Server: db
Username: MYSQL_USER
Password: MYSQL_PASSWORD
```

### Database connection in PHP

The database connection is configured in:

```text
src/config.php
```

The application uses PDO and reads database credentials from `.env`.

### Git ignore

Do not commit:

```text
.env
/vendor/
/.idea/
/.vscode/
/storage/logs/*
*.log
.DS_Store
```

Commit these files:

```text
.env.example
docker-compose.yml
php/Dockerfile
database/schema.sql
database/seed.sql
composer.json
composer.lock
public/
src/
views/
README.md
```

### Composer dependencies

Install dependencies locally if needed:

```bash
composer install
```

```
```
