docker-up:
	docker-compose $(COMPOSE_LOCAL_FILES) up --build -d

docker-exec-php:
	docker-compose $(COMPOSE_LOCAL_FILES) exec php bash

cs:
	vendor/bin/php-cs-fixer fix src --verbose --rules=@Symfony,@PhpCsFixer

migrate:
	./bin/console --no-interaction doctrine:migrations:migrate

docker-up-install: docker-up
	docker exec -it $(shell basename $(CURDIR))_php_1 composer install

docker-migrate:
	docker exec -it $(shell basename $(CURDIR))_php_1 make migrate