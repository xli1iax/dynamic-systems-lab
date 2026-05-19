
## Slim backend setup

Project uses Slim Framework as a lightweight PHP backend framework.  
The application runs in Docker with Apache, PHP, MariaDB, Octave and phpMyAdmin.

The backend provides:
- protected CAS API endpoint;
- API key authentication middleware;
- Octave execution service;
- separate endpoints for dynamic system animations;
- request logging into MariaDB;
- frontend-ready JSON responses for graphs and animations.

---

## Project structure

```text
database/
  schema.sql
  seed.sql

docker/
  apache.conf

nginx/
  default.conf

php/
  Dockerfile

public/
  index.php
  .htaccess
  css/
  js/

src/
  Controllers/
    HomeController.php
    CasController.php
    AnimationController.php
    LogController.php

  Middleware/
    ApiKeyMiddleware.php

  Services/
    DatabaseService.php
    OctaveService.php
    AnimationService.php
    LogService.php

  Models/
    Log.php

  config.php

views/
storage/logs/
````

---

## Installed dependencies

```bash
composer require slim/slim slim/psr7 symfony/process monolog/monolog vlucas/phpdotenv
composer require zircote/swagger-php 
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
```

`API_KEY` is used for protected API access.
`CAS_DELAY_MS` defines artificial server-side delay for CAS calculations in milliseconds.

---

## API key protection

All CAS and animation API endpoints are protected by `ApiKeyMiddleware`.

Every request must contain header:

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

## API endpoints

### 1. Execute custom CAS command

```text
POST /api/cas/execute
```

This endpoint is used by the web form where the user enters an Octave/CAS command manually.

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

Example request for graph data:

```json
{
  "command": "linspace(0,10,5)",
  "source": "form"
}
```

Example response:

```json
{
  "success": true,
  "result": [0, 2.5, 5, 7.5, 10]
}
```

Dangerous commands are blocked, for example:

```text
system, unix, dos, delete, rmdir, mkdir, fopen, save, load, cd, ls, exit, quit
```



### CAS supports session-based helper variables between consecutive requests from the same user session.

Example:

Request 1:

```
{
  "command": "a=1+1",
  "source": "form"
}
```
Response:
```
{
"success": true,
"result": 2
}
```
Request 2:
```
{
"command": "a+2",
"source": "form"
}
```
Response:
```
{
"success": true,
"result": 4
}
```
Variables can also be updated:
```
{
"command": "a=a+2",
"source": "form"
}
```
This functionality allows preserving temporary CAS variables required by the assignment.


---

### 2. Inverted pendulum animation data

```text
POST /api/animations/pendulum
```

This endpoint calculates data for the inverted pendulum animation.

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

The frontend can use:

* `time` as chart labels;
* `position` for cart/pendulum position graph;
* `angle` for pendulum angle graph;
* `finalState` as initial state for the next simulation step.

---

### 3. Ball and beam animation data

```text
POST /api/animations/ball-beam
```

This endpoint calculates data for the ball and beam animation.

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

The frontend can use:

* `time` as chart labels;
* `position` for ball position;
* `angle` for beam angle;
* both arrays for synchronized graph and animation rendering.

---

### 4. Logs export




### Additional Backend Endpoints

Implemented REST API endpoints for logs and animation statistics.

#### CAS Request Logs
Added endpoints for accessing and exporting logged CAS requests:

- `GET /api/logs`  
  Returns all stored CAS request logs in JSON format, including:
    - request source
    - executed command
    - calculation result
    - success/error status
    - error message
    - IP address
    - timestamp

- `GET /api/logs/export`  
  Exports all stored CAS logs as a downloadable CSV file.

---

#### Animation Usage Statistics
Implemented endpoints for monitoring animation usage statistics.

- `GET /api/statistics/animations`  
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
``

* `GET /api/statistics/animations/{name}`
  Returns detailed usage records for a selected animation.

  Returned data includes:

    * anonymous user token
    * city
    * country
    * timestamp of usage

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

These endpoints are protected using API key authentication.





## CAS and animation architecture

The application uses this backend flow:

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

For manual CAS commands:

```text
CasController -> OctaveService -> LogService -> Log model
```

For animations:

```text
AnimationController -> AnimationService -> OctaveService -> LogService -> Log model
```

Animation endpoints do not accept raw Octave code.
They accept only numeric parameters. The Octave script is generated on the backend side.

---

## Request logging

All requests sent to CAS are stored in the database.

Database table:

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

## phpMyAdmin login

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

## Current implemented functionality

Implemented backend functionality:

* Slim project structure;
* Docker environment with Apache, PHP, MariaDB and phpMyAdmin;
* PDO database connection through `.env`;
* API key authentication middleware;
* protected CAS endpoint;
* Octave command execution through `Symfony Process`;
* JSON response normalization for frontend;
* server-side CAS delay using `CAS_DELAY_MS`;
* request logging into MariaDB;
* separate animation endpoint for inverted pendulum;
* separate animation endpoint for ball and beam;
* frontend-ready arrays for synchronized graph and animation.
* session-based CAS variable persistence between requests;
* support for user-defined helper variables in CAS calculations;

Still to implement:

* frontend textarea with syntax highlighting;
* frontend graph rendering;
* frontend animation rendering;
* CSV export of logs;
* OpenAPI documentation;
* dynamic PDF documentation;
* animation usage statistics;
* final video.

## Implemented Features

### Main Dashboard
Implemented a responsive homepage dashboard that serves as the central navigation point of the application.  
The homepage provides access to all major system modules, including:

- CAS manual command execution
- Dynamic system simulations
- Animation statistics
- CAS request logs with export
- OpenAPI API documentation
- PDF documentation section

The interface was designed as a clean dashboard layout for easier navigation between project functionalities.

### OpenAPI Documentation
Implemented interactive API documentation using Swagger UI and OpenAPI 3.0.

Documented backend endpoints include:

- `POST /api/cas/execute` — execute CAS/Octave commands
- `POST /api/animations/pendulum` — inverted pendulum simulation
- `POST /api/animations/ball-beam` — ball and beam simulation
- `GET /api/statistics/animations` — animation usage summary
- `GET /api/statistics/animations/{name}` — animation usage details
- `GET /api/logs` — retrieve CAS request logs
- `GET /api/logs/export` — export logs as CSV

Swagger documentation supports:

- interactive endpoint testing
- API key authentication
- request/response schema visualization
- grouped endpoint organization

  