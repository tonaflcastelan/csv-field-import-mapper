install:
	@echo "Installing dependencies"
	@if [ ! -f .env ]; then cp .env.example .env; fi
	@( \
		composer install; \
	)

app_key:
	@echo "Generate key"
	@php artisan key:generate

start:
	@echo "Init Laravel Server"
	@php artisan serve

migrate:
	@echo "Execute migrations"
	@php artisan migrate

seed:
	@echo "Execute seeders"
	@php artisan db:seed

config_clear:
	@echo "Clean config cache"
	@php artisan config:clear

cache_clear:
	@echo "Clean app cache"
	@php artisan cache:clear
