## Project Manager V2 - Docker Commands

.PHONY: up down build restart shell artisan composer npm logs fresh

## Start all containers
up:
	docker compose up -d

## Stop all containers
down:
	docker compose down

## Build and start containers
build:
	docker compose up -d --build

## Restart all containers
restart:
	docker compose restart

## Access the PHP container shell
shell:
	docker compose exec app bash

## Run artisan commands
## Usage: make artisan CMD="migrate"
artisan:
	docker compose exec app php artisan $(CMD)

## Run composer commands
## Usage: make composer CMD="require package/name"
composer:
	docker compose exec app composer $(CMD)

## Run npm commands
## Usage: make npm CMD="install"
npm:
	docker compose exec app npm $(CMD)

## Show logs
logs:
	docker compose logs -f

## Run migrations fresh with seeds
fresh:
	docker compose exec app php artisan migrate:fresh --seed

## Run migrations
migrate:
	docker compose exec app php artisan migrate

## Generate app key
key:
	docker compose exec app php artisan key:generate

## Install project dependencies (first time setup)
install:
	docker compose exec app composer install
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate
	docker compose exec app npm install
	docker compose exec app npm run build

## Run vite dev server
dev:
	docker compose exec app npm run dev
