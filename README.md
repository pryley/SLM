# SLM

A Software License Manager built on Lumen.

# Installation

1. composer install
2. php artisan migrate
3. php artisan slm:install

# Artisan Commands

### List all of the oauth clients

`php artisan slm:clients`

### Create a new user

`php artisan slm:user`

### List all of the users

`php artisan slm:users`

### Get the access token for an oauth client

`php artisan slm:access-token`
