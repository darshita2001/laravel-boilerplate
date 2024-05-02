
# Laravel Boilerplate
It is a Laravel application with integrated jwt authentication and a global helper function for logging and JSON response.
- [Prerequisites](#Prerequisites)
- [Installation](#Installation)

## Prerequisites
1. PHP >= 8.2
2. Composer
3. Apache
4. mySql
5. Laravel

## Installation
1. Copy .env.example file and rename it to .env
   - ```cp .env.example .env```
2. create storage folder inside main directory
   - ```cd storage/```
   - ```mkdir -p framework/{sessions,views,cache}```
   - ```chmod -R 775 framework```
3. Run command :-
   - ```composer install```
   - ```composer update```
   - ```composer dumpa```
   - ``` php artisan jwt:secret ```
4. Set .env file
   - APP_KEY, APP_URL, DB_DATABASE, DB_USERNAME, DB_PASSWORD
   - For generating APP_KEY - ``` php artisan key:generate ```
5. Migrate database :- ``` php artisan migrate ```
6. Seed database :-
    - ``` php artisan db:seed ```
7. Serve project :- ``` php artisan serve ```
