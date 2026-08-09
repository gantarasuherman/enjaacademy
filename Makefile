# Convenience wrapper around Laradock (Linux / macOS / WSL).
# Windows users: run ./ld.ps1 instead — same commands, no `make` needed.
#
# Laradock lives in ./laradock and mounts this directory at /var/www, so every
# artisan/composer/npm command runs inside its `workspace` container. Ports and
# service versions are configured in laradock/.env, never here.

DC       := docker compose --project-directory laradock -f laradock/docker-compose.yml
SERVICES := nginx mysql redis phpmyadmin mailpit workspace

# The workspace image already has /var/www as its working directory, so no
# --workdir flag is needed (and passing one breaks under Git Bash, which
# rewrites absolute paths).
WS       := $(DC) exec -T workspace
WS_TTY   := $(DC) exec workspace

.DEFAULT_GOAL := help
.PHONY: help up down restart build logs ps install migrate fresh seed cache-clear backup \
        shell tinker test lint assets spa-dev spa-build deploy-spa queue schedule

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'

## ---------------------------------------------------------------- lifecycle

up: ## Start the Laradock stack
	$(DC) up -d $(SERVICES)

down: ## Stop the stack
	$(DC) down

restart: ## Restart the stack
	$(DC) restart $(SERVICES)

build: ## Rebuild the workspace and php-fpm images
	$(DC) build workspace php-fpm

ps: ## Show container status
	$(DC) ps

logs: ## Tail nginx and php-fpm logs
	$(DC) logs -f nginx php-fpm

queue: ## Start the queue worker (php-worker)
	$(DC) up -d php-worker

schedule: ## Run the Laravel scheduler in the background
	$(DC) exec -d workspace php artisan schedule:work

## ------------------------------------------------------------------ laravel

install: up ## First-time setup: dependencies, key, migrate, seed, build assets
	$(WS) composer install --no-interaction
	$(WS) php artisan key:generate --force
	$(WS) php artisan storage:link
	$(WS) php artisan migrate --force
	$(WS) php artisan db:seed --force
	$(MAKE) assets
	@echo ""
	@echo "  App / admin : http://localhost:8000/admin"
	@echo "  phpMyAdmin  : http://localhost:8080"
	@echo "  Mailpit     : http://localhost:8025"
	@echo "  Login       : superadmin@nihongo.test / password"

migrate: ## Run pending migrations
	$(WS) php artisan migrate --force

fresh: ## Drop everything and re-seed (destructive)
	$(WS) php artisan migrate:fresh --seed --force

seed: ## Re-run seeders
	$(WS) php artisan db:seed --force

cache-clear: ## Clear config, route, view and menu caches
	$(WS) php artisan optimize:clear
	$(WS) php artisan menu:clear

backup: ## Create a database backup
	$(WS) php artisan backup:run

shell: ## Shell into the workspace container
	$(WS_TTY) bash

tinker: ## Open tinker
	$(WS_TTY) php artisan tinker

test: ## Run the test suite
	$(WS) php artisan test

lint: ## Format PHP with Pint
	$(WS) ./vendor/bin/pint

## ----------------------------------------------------------------- frontend

assets: ## Build admin-panel assets (Blade/Alpine/Tailwind)
	$(WS) npm install --no-audit --no-fund
	$(WS) npm run build

spa-build: ## Build the React SPA
	$(WS) bash -lc 'cd /var/www/frontend && npm install --no-audit --no-fund && npm run build'

spa-dev: ## Run the React SPA dev server (host port 5173)
	$(WS_TTY) bash -lc 'cd /var/www/frontend && npm run dev -- --host 0.0.0.0'

deploy-spa: spa-build ## Build the SPA and publish it into public/app
	$(WS) bash -lc 'rm -rf /var/www/public/app && mkdir -p /var/www/public/app && cp -r /var/www/frontend/dist/. /var/www/public/app/'
	@echo "SPA published to public/app — learner routes now serve the build."
