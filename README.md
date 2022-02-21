# Scheduler Services API
___
Hello, with the purpose of assisting customers and workers in hiring necessary and offered services, the scheduler-services project was developed, which allows customers to hire workers easily and quickly and workers can make their skills available effortlessly focusing only on what matters, the completion of the tasks.

## Dependencies
___
| Dependency | Version|
|:------------|-------|
|php|^7.4 - 8.0|
| laravel/lumen-framework | ^8.* |
|tymon/jwt-auth|^1.0|
|darkaonline/swagger-lume|8.*|

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

- Fill in the required fields for database connection in the .env.

- Set APP_KEY option of your .env file to a 32 character random string.

- Set JWT_SECRET option of your .env file to a 64 character random string.

Execute the command line for testing verification:
```
./vendor/bin/phpunit
```

Execute the command to start the server:
```
php -S localhost:8000 public/index.php
```

### API Documentation
The API documentation using swagger can be access in the routes:
```
http://localhost:8000/api/documentation
```
```
http://localhost:8000/docs
```


