OS := $(shell uname)

start_dev:
ifeq ($(OS),Darwin)
	docker volume create --name=app-sync
	docker-compose -f docker-compose.yml up -d
	docker-sync start
else
	docker-compose up -d
endif

stop_dev:           ## Stop the Docker containers
ifeq ($(OS),Darwin)
	docker-compose stop
	docker-sync stop
else
	docker-compose stop
endif