# Tile Price Scraper API (Symfony 7.4)

REST/SOAP API для получения цен на плитку и управления заказами с поддержкой полнотекстового поиска через Manticore Search.

## Технологический стек

- **Framework**: Symfony 7.4
- **PHP**: 8.3
- **Database**: PostgreSQL 15
- **Search**: Manticore Search
- **Containerization**: Docker & Docker Compose
- **Testing**: PHPUnit

## Быстрый старт

### Требования

- Docker 20.10+
- Docker Compose 2.0+
- Make (опционально)

### Запуск через Make

```bash
# Сборка и запуск приложения
make up

# Просмотр логов приложения
make logs

# Остановка всех контейнеров
make down

# Запуск тестов
make test

# Подготовка тестовой базы данных
make test-db-setup

# Пересборка контейнеров (без кэша)
make rebuild

# Запуск миграций базы данных
make migrate

# -----------------------------
# Дополнительные команды
# -----------------------------

# Показать все доступные make-команды
make help

# Сборка контейнеров
make build

# Просмотр логов базы данных
make logs-db

# Вход в shell контейнера приложения
make shell

# Выполнить команду внутри контейнера приложения
make exec-app

# Вход в PostgreSQL shell
make db-shell

# Выполнить команду внутри контейнера базы данных
make exec-db

# Запуск тестов с отчётом покрытия
make test-coverage

# Сброс базы данных (ВНИМАНИЕ: удаляет все данные)
make db-reset

# Установка зависимостей Composer
make composer-install

# Очистка кэша Symfony
make cache-clear

# Показать статус контейнеров
make status

# Перезапуск всех контейнеров
make restart

# Удаление контейнеров и volume (полная очистка)
make clean
```

### Запуск через Docker Compose

```bash
# Сборка и запуск
docker-compose up --build -d

# Просмотр логов
docker-compose logs -f app

# Остановка
docker-compose down

# Запуск тестов
docker-compose exec app php bin/phpunit

# Миграции
docker-compose exec app php bin/console doctrine:migrations:migrate -n
```

## Конфигурация

Настройки в `.env`:

```env
# APP
APP_ENV=dev
APP_SECRET=your-secret-key-here
NGINX_PORT=8080

# Database
POSTGRES_HOST=db
POSTGRES_PORT=5432
POSTGRES_DB=tiledb
POSTGRES_USER=postgres
POSTGRES_PASSWORD=postgres
DATABASE_URL="postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@${POSTGRES_HOST}:${POSTGRES_PORT}/${POSTGRES_DB}?serverVersion=15&charset=utf8"


# Manticore Search
MANTICORE_HOST=manticore
MANTICORE_PORT=9308
MANTICORE_SQL_PORT=9306

TILE_EXPERT_BASE_URL=https://tile.expert/en-us/tile
```

## API Endpoints

После запуска доступна документация:
- **Swagger UI**: http://localhost:8000/api/doc
- **OpenAPI JSON**: http://localhost:8000/api/doc.json

Сюда же добавил dump (улучшение базы данных) и данные postman (cм. файлы) 
`postman-request-response.zip` и
`tili-2026_01_20_14_54_15-dump.sql`


## Тестирование

```bash
# Запуск всех тестов
make test

# Подготовка тестовой базы данных
make test-db-setup

# Или через docker-compose
docker-compose exec app php bin/phpunit

# С покрытием кода
docker-compose exec app php bin/phpunit --coverage-html coverage

# Конкретный тест
docker-compose exec app php bin/phpunit tests/Controller/PriceControllerTest.php
```
