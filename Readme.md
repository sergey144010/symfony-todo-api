# Install project
- [Install docker](https://docs.docker.com/), if not already installed
- [Install docker compose V2](https://docs.docker.com/compose/install/linux/), if not already installed
- Clone project: `git clone git@github.com:sergey144010/symfony-todo-api.git`
- `cd symfony-todo-api/`
- `./develop make-project`

# Code style, static analise, tests
- `./develop docker-check`

included
- `./develop docker-phpcs`
- `./develop docker-phpstan`
- `./develop docker-phpunit`

# Start/stop/remove project
- `./develop stop`
- `./develop start`
- `./develop remove-project`
- `./develop help`

# About
Api host `localhost:8080`

Api resources:
- `/api/register` user registration
- `/api/token` take user token
- `/api/task` get list tasks (GET), create task (POST)
- `/api/task/{id}` get task (GET), update task (PATCH), delete task (DELETE)
- `/api/doc` api doc

# Postman
Postman collection for import by path `./http/postman_collection.json`
and auto documentation

# Api documentation
- `/api/doc`
