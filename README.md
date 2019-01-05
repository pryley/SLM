# SLM

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/geminilabs/SLM/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/geminilabs/SLM/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/geminilabs/SLM/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/geminilabs/SLM/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/geminilabs/SLM/badges/build.png?b=master)](https://scrutinizer-ci.com/g/geminilabs/SLM/build-status/master)

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
