# Task management application
A simple task management application

## Prerequisites
You need to install [Docker](https://docs.docker.com/engine/install/) and [Docker Compose](https://docs.docker.com/compose/install/).

## Getting started
Run:

```
$ docker compose up
```

By default, the application uses port 5000, but it is possible to change it by setting the environment variable `$SERVER_PORT`.

## Tests
To run the tests, run the following command:

```
$ docker compose exec php artisan test
```
