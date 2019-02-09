docker_nginx = sf4_app_nginx_1
docker_php = sf4_app_php-cli_1
docker_db = sf4_app_mysql_1

docker-up:
	@sudo docker-compose up -d

docker-build:
	@sudo docker-compose up --build -d

docker-down:
	@sudo docker-compose down

docker-stop:
	@sudo docker-compose stop

docker-show:
	@sudo docker ps

permissions:
	@sudo chmod 777 -R app/var

connect-nginx:
	@sudo docker exec -it $(docker_nginx) bash

connect-php:
	@sudo docker exec -it $(docker_php) bash

connect-db:
	@sudo docker exec -it $(docker_db) mysql -uroot -proot

php:
	docker-compose exec app-php-cli php bin/console $(cmd)