



## Slim backend setup

Project uses Slim Framework as a lightweight PHP backend framework.

### Project structure

```text
public/
  index.php          # front controller, all routes start here
  .htaccess          # redirects all requests to index.php
  css/
  js/

src/
  Controllers/       # request handlers
  Services/          # business logic, Octave, PDF, CSV, etc.
  Models/            # database models

views/               # PHP templates
storage/logs/        # application logs
````

### Installed dependencies

```bash
composer require slim/slim slim/psr7 symfony/process monolog/monolog vlucas/phpdotenv
```

### Run project with Docker

```bash
docker compose down
docker compose build --no-cache
docker compose up
```

Application:

```text
http://localhost:8080/
```

Test API endpoint:

```text
http://localhost:8080/api/test
```

### Docker notes

Apache DocumentRoot is set to:

```text
/var/www/html/public
```

Apache rewrite module is enabled, so routes like `/api/test` are redirected to `public/index.php`.

### Git ignore

Do not commit:

```text
/vendor/
/.env
/.idea/
/storage/logs/*
```

Composer dependencies are installed by:

```bash
composer install
```
