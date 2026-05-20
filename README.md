
# Dynamic Systems Lab

Web application for working with dynamic system simulations and CAS calculations.

The project uses Slim Framework as a lightweight PHP backend.  
The application runs in Docker with PHP, MariaDB, Octave and phpMyAdmin.

## Main functionality

- manual CAS command execution through Octave;
- API key protected backend endpoints;
- dynamic system simulations:
  - inverted pendulum;
  - ball and beam;
- request logging into MariaDB;
- animation usage statistics;
- CSV export of CAS logs;
- OpenAPI documentation with Swagger UI;
- frontend pages for CAS, simulations, logs and statistics.

---

## Project structure

```text
dynamic-systems-lab/
├── database/
│   ├── schema.sql
│   └── seed.sql
│
├── docker/
│   └── apache.conf
│
├── nginx/
│   └── default.conf
│
├── php/
│   └── Dockerfile
│
├── public/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── app.js
│   │   └── ball-beam.js
│   ├── openapi/
│   ├── .htaccess
│   └── index.php
│
├── src/
│   ├── Controllers/
│   │   ├── AnimationController.php
│   │   ├── AnimationStatisticsController.php
│   │   ├── CasController.php
│   │   ├── DocumentationController.php
│   │   ├── HomeController.php
│   │   └── LogController.php
│   │
│   ├── Middleware/
│   │   └── ApiKeyMiddleware.php
│   │
│   ├── Models/
│   │   └── Log.php
│   │
│   ├── Services/
│   │   ├── AnimationService.php
│   │   ├── AnimationStatisticsService.php
│   │   ├── AnimationUsageService.php
│   │   ├── DatabaseService.php
│   │   ├── LogService.php
│   │   └── OctaveService.php
│   │
│   ├── config.php
│   └── lang.php
│
├── storage/
│
├── vendor/
│
├── views/
│   ├── api-docs.php
│   ├── ball-beam.php
│   ├── cas.php
│   ├── home.php
│   ├── inverted-pendulum.php
│   ├── logs.php
│   └── statistics.php
│
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
├── docker-compose.yml
└── README.md
````

---

## Installed dependencies

```bash
composer require slim/slim slim/psr7 symfony/process monolog/monolog vlucas/phpdotenv
composer require zircote/swagger-php
composer require dompdf/dompdf -W
composer update symfony/process -W
```

---

## Environment configuration

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

API_KEY=your_secret_api_key
CAS_DELAY_MS=300

ANIMATION_STATS_INTERVAL_MINUTES=10
```

`API_KEY` is used for protected API access.
`CAS_DELAY_MS` defines artificial server-side delay for CAS calculations in milliseconds.

---

## Run project with Docker

```bash
docker compose up -d --build
```

Application:

```text
http://localhost:8080/
```

phpMyAdmin:

```text
http://localhost:8081/
```

---

## phpMyAdmin login

Use credentials from `.env`:

```text
Server: db
Username: MYSQL_USER
Password: MYSQL_PASSWORD
```

---

## API key protection

CAS and animation API endpoints are protected by `ApiKeyMiddleware`.

Every protected request must contain this header:

```text
X-API-KEY: your_secret_api_key
```

If the key is missing or invalid, the backend returns:

```json
{
  "error": "Unauthorized. Invalid API key."
}
```

with HTTP status `401`.

---

## API endpoints

### Execute CAS command

```text
POST /api/cas/execute
```

This endpoint is used by the CAS form where the user enters an Octave command manually.

Headers:

```text
Content-Type: application/json
X-API-KEY: your_secret_api_key
```

Example request:

```json
{
  "command": "2+2",
  "source": "form"
}
```

Example response:

```json
{
  "success": true,
  "result": 4
}
```

Example request with variable:

```json
{
  "command": "a=1+1",
  "source": "form"
}
```

Example response:

```json
{
  "success": true,
  "result": 2
}
```

Variables are stored between consecutive requests from the same user session.

Example next request:

```json
{
  "command": "a+2",
  "source": "form"
}
```

Example response:

```json
{
  "success": true,
  "result": 4
}
```

Dangerous commands are blocked, for example:

```text
system, unix, dos, delete, rmdir, mkdir, fopen, save, load, cd, ls, exit, quit
```

---

### Inverted pendulum animation

```text
POST /api/animations/pendulum
```

Example request:

```json
{
  "r": 0.2,
  "duration": 10,
  "step": 0.05,
  "initPosition": 0,
  "initVelocity": 0,
  "initAngle": 0,
  "initAngularVelocity": 0
}
```

Example response structure:

```json
{
  "success": true,
  "animation": "inverted_pendulum",
  "data": {
    "time": [0, 0.05, 0.1],
    "position": [0, 0.001, 0.003],
    "angle": [0, -0.001, -0.002],
    "state": [],
    "finalState": [],
    "target": 0.2
  }
}
```

The frontend uses this data for synchronized graph and animation rendering.

---

### Ball and beam animation

```text
POST /api/animations/ball-beam
```

Example request:

```json
{
  "r": 0.25,
  "duration": 5,
  "step": 0.01,
  "initPosition": 0,
  "initVelocity": 0,
  "initAngle": 0,
  "initAngularVelocity": 0
}
```

Example response structure:

```json
{
  "success": true,
  "animation": "ball_beam",
  "data": {
    "time": [0, 0.01, 0.02],
    "position": [0, 0.001, 0.002],
    "angle": [0.0001, 0.00009, 0.00008],
    "state": [],
    "finalState": [],
    "target": 0.25
  }
}
```

The frontend uses:

* `time` as chart labels;
* `position` for ball position;
* `angle` for beam angle;
* `finalState` for stable repeated simulation runs.

---

### CAS request logs

```text
GET /api/logs
```

Returns all stored CAS request logs in JSON format.

Returned data includes:

* request source;
* executed command;
* calculation result;
* success/error status;
* error message;
* IP address;
* timestamp.

---

### Export CAS logs

```text
GET /api/logs/export
```

Exports stored CAS logs as a downloadable CSV file.

---

### Animation usage statistics

```text
GET /api/statistics/animations
```

Returns summary statistics showing how many times each animation was used.

Example response:

```json
{
  "success": true,
  "data": [
    {
      "animation_name": "inverted_pendulum",
      "total_uses": 1
    }
  ]
}
```

---

### Detailed animation statistics

```text
GET /api/statistics/animations/{name}
```

Returns detailed usage records for selected animation.

Example:

```json
{
  "success": true,
  "animation": "inverted_pendulum",
  "data": [
    {
      "animation_name": "inverted_pendulum",
      "user_token": "...",
      "city": null,
      "country": null,
      "used_at": "2026-05-19 13:08:11"
    }
  ]
}
```

---

## OpenAPI documentation

The project contains OpenAPI documentation with Swagger UI.

Documented endpoints include:

* `POST /api/cas/execute`;
* `POST /api/animations/pendulum`;
* `POST /api/animations/ball-beam`;
* `GET /api/logs`;
* `GET /api/logs/export`;
* `GET /api/statistics/animations`;
* `GET /api/statistics/animations/{name}`.

Swagger UI supports:

* interactive endpoint testing;
* API key authentication;
* request and response schema visualization;
* grouped endpoint organization.

---

## Backend architecture

General request flow:

```text
Request
  -> ApiKeyMiddleware
  -> Controller
  -> Service
  -> OctaveService
  -> Octave
  -> JSON response
  -> LogService
  -> Database
```

CAS command flow:

```text
CasController
  -> OctaveService
  -> LogService
  -> Log model
```

Animation flow:

```text
AnimationController
  -> AnimationService
  -> OctaveService
  -> AnimationUsageService
  -> LogService
  -> Database
```

Animation endpoints do not accept raw Octave code.
They accept only numeric parameters, and the Octave script is generated on the backend side.

---

## Request logging

All CAS requests are stored in the database table:

```text
logs
```

Stored fields:

```text
id
source
command
result
success
error_message
ip_address
created_at
```

Recommended column types:

```sql
command LONGTEXT NOT NULL,
result LONGTEXT NULL,
error_message LONGTEXT NULL
```

Source examples:

```text
form
animation
api
```

Logged information includes:

* date and time;
* sent command or animation parameters;
* result;
* success/error state;
* error message if calculation failed;
* IP address.

---

## Database

MariaDB runs in a separate Docker container.

Database data is stored in Docker volume:

```yaml
volumes:
  - db_data:/var/lib/mysql
```

SQL files are mounted into MariaDB initialization folder:

```yaml
db:
  image: mariadb:11
  volumes:
    - db_data:/var/lib/mysql
    - ./database/schema.sql:/docker-entrypoint-initdb.d/01_schema.sql
    - ./database/seed.sql:/docker-entrypoint-initdb.d/02_seed.sql
```

SQL files are executed automatically only when the database volume is created for the first time.

If `schema.sql` or `seed.sql` was changed and the local database must be recreated, run:

```bash
docker compose down -v
docker compose up -d --build
```

Warning: this deletes the local database data.

---

## Frontend pages

The application contains several frontend views:

* `home.php` — main dashboard;
* `cas.php` — manual CAS command form;
* `inverted-pendulum.php` — inverted pendulum simulation page;
* `ball-beam.php` — ball and beam simulation page;
* `logs.php` — CAS request logs page;
* `statistics.php` — animation statistics page;
* `api-docs.php` — OpenAPI documentation page.

Frontend scripts are stored in:

```text
public/js/
```

Main styles are stored in:

```text
public/css/style.css
```

---

## Git ignore

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

---

## Implemented functionality

Implemented backend functionality:

* Slim project structure;
* Docker environment with PHP, MariaDB, Octave and phpMyAdmin;
* PDO database connection through `.env`;
* API key authentication middleware;
* protected CAS endpoint;
* Octave command execution through Symfony Process;
* JSON response normalization for frontend;
* server-side CAS delay using `CAS_DELAY_MS`;
* request logging into MariaDB;
* CSV export of CAS logs;
* session-based CAS variable persistence between requests;
* support for user-defined helper variables in CAS calculations;
* separate animation endpoint for inverted pendulum;
* separate animation endpoint for ball and beam;
* frontend-ready arrays for synchronized graph and animation;
* animation usage statistics;
* OpenAPI documentation;
* dashboard navigation between project modules.

---

## Notes

The `.env` file must stay local and must not be pushed to GitHub.

If the database schema was changed after containers were already started, the database volume has to be recreated with:

```bash
docker compose down -v
docker compose up -d --build
```

```
```
