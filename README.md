# Service Scheduler API
___
Hello, with the purpose of assisting customers and workers in hiring necessary and offered services, the scheduler-services project was developed, which allows customers to hire workers easily and quickly and workers can make their skills available effortlessly focusing only on what matters, the completion of the tasks.


## Dependencies
___
| Dependency | Version|Doc|
|:------------|-------|----|
|php|^7.4 - 8.0|
| laravel/lumen-framework | ^8.* |https://lumen.laravel.com/docs/8.x|
|tymon/jwt-auth|^1.0|https://jwt-auth.readthedocs.io/en/develop/|
|darkaonline/swagger-lume|8.*|https://github.com/DarkaOnLine/SwaggerLume

## Requirement
___
Postgresql Database and the drivers activated on the PHP Server.

## Installation
___
```
git clone https://github.com/JuanCarlos94/service-scheduler.git
```
```
cd service-scheduler
```
```
composer install
```

Create an .env file with the .env.example file structure

- Set the DB_* keys for database configuration connection. (default: pgsql)

- Set APP_KEY option of your .env file to a 32 character random string.

- Execute ``php artisan jwt:secret`` to set JWT_SECRET.

Execute the command line for testing verification:
```
php ./vendor/bin/phpunit
```

Execute the command to initialize the database:
```
php artisan migrate
```

API Documentation Configuration:
- Run ``php artisan swagger-lume:publish-config`` to publish configs (config/swagger-lume.php)

- Run ``php artisan swagger-lume:publish-views`` to publish views (resources/views/vendor/swagger-lume)

- Run ``php artisan swagger-lume:publish`` to publish everything

- Run ``php artisan swagger-lume:generate`` to generate docs

Execute the command to start the server:
```
php -S localhost:8000 public/index.php
```

The API documentation using swagger can be access in the routes:
```
http://localhost:8000/api/documentation
```
```
http://localhost:8000/docs
```






