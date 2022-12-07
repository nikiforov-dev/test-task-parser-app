docker-up:
	docker-compose $(COMPOSE_LOCAL_FILES) up --build -d

docker-exec-php:
	docker-compose $(COMPOSE_LOCAL_FILES) exec php bash