init: docker-down docker-pull docker-build docker-up project-init

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

project-init: project-composer-install project-wait-mysql-db project-migrations project-fixtures

project-composer-install:
	docker-compose run --rm php-cli composer install

project-wait-mysql-db:
	until docker-compose exec -T mysql /usr/bin/mysql -uroot -proot --execute "SHOW DATABASES;" ; do sleep 1 ; done

project-migrations:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

project-fixtures:
	docker-compose run --rm php-cli php bin/console doctrine:fixtures:load --no-interaction
