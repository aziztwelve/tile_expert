.PHONY: help up down build rebuild logs shell test migrate db-reset composer-install

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Start all containers
	docker compose up -d
	@echo "Application started at http://localhost:8080"
	@echo "API Documentation: http://localhost:8080/api/doc"

down: ## Stop all containers
	docker compose down

build: ## Build containers
	docker compose build

rebuild: ## Rebuild and start containers
	docker compose down
	docker compose build --no-cache
	docker compose up -d

logs: ## Show logs
	docker compose logs -f app

logs-db: ## Show database logs
	docker compose logs -f db

shell: ## Enter app container shell
	docker compose exec app sh

exec-app: ## Exec app container
	docker compose exec app

db-shell: ## Enter PostgreSQL shell
	docker compose exec db psql -U postgres -d tiledb

exec-db: ## Exec db container
	docker compose exec db

test: ## Run tests
	docker compose exec -e APP_ENV=test app php bin/phpunit

test-db-setup: #Setup test db
	docker compose exec -e APP_ENV=test app php bin/console doctrine:database:create
	docker compose exec -e APP_ENV=test app php bin/console doctrine:migrations:migrate --no-interaction
	docker compose exec -e APP_ENV=test app php bin/console doctrine:fixtures:load --no-interaction

test-coverage: ## Run tests with coverage
	docker compose exec -e APP_ENV=test app php bin/phpunit --coverage-html coverage

migrate: ## Run database migrations
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

db-reset: ## Reset database (WARNING: deletes all data)
	docker compose exec app php bin/console doctrine:database:drop --force --if-exists
	docker compose exec app php bin/console doctrine:database:create
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

composer-install: ## Install composer dependencies
	docker compose exec app composer install

cache-clear: ## Clear Symfony cache
	docker compose exec app php bin/console cache:clear

status: ## Show container status
	docker compose ps

restart: ## Restart all containers
	docker compose restart

clean: ## Remove containers and volumes
	docker compose down -v
	rm -rf var/cache/* var/log/*
