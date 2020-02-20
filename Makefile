include .env
export

all:
	echo "Use same command"

start:
	mkdir -p var/cache
	mkdir -p var/log
	mkdir -p vendor
	mkdir -p bin/.phpunit
	mkdir -p web/build

	cp phpunit.xml.dist phpunit.xml

	docker-compose -f docker-compose.dev.yml up --force-recreate --detach
	mutagen daemon start
	mutagen project start

	# nginx
	mutagen create \
		--default-owner-beta=nginx \
		--scan-mode accelerated \
		--sync-mode one-way-replica \
		--watch-mode-beta no-watch \
		web/ docker://nginx@ad-nginx-dev/var/www/html/web

	docker exec -u www-data -t ad-php-dev php composer.phar install -o --no-scripts
	docker exec -u www-data -t ad-php-dev php bin/phpunit install
	docker exec -u www-data -t ad-php-dev php bin/console cache:clear

	echo "docker exec -t -u www-data ad-php-dev php composer.phar run-script php-cs && sleep 2 && git add -u" | tee .git/hooks/pre-commit
	chmod +x .git/hooks/pre-commit

	# make sync
	docker cp ad-php-dev:/var/www/html/var/cache/    ./var/

stop:
	pkill autossh || true
	mutagen project terminate || true
	mutagen terminate -a || true
	mutagen daemon stop || true
	docker-compose -f docker-compose.dev.yml down
	rm -rf web/build/*
	rm -rf var/cache/dev/*
	rm -rf var/cache/prod/*
	rm -rf var/cache/test/*
	> var/log/dev.log
	> var/log/test.log
	> var/log/prod.log

sync:
	rm -rf vendor/*
	docker cp ad-php-dev:/var/www/html/vendor        ./
	docker cp ad-php-dev:/var/www/html/var/cache/    ./var/
	docker cp ad-php-dev:/var/www/html/bin/          ./
	docker cp ad-php-dev:/var/www/html/composer.json ./composer.json
	docker cp ad-php-dev:/var/www/html/composer.phar ./composer.phar
	docker cp ad-php-dev:/var/www/html/composer.lock ./composer.lock
	docker cp ad-php-dev:/var/www/html/symfony.lock  ./symfony.lock

build:
	export DOCKER_BUILDKIT=1
	docker-compose -f docker-compose.dev.yml build \
		--build-arg APP_DEBUG=$$APP_DEBUG \
		--build-arg APP_DEBUG_ADDRESS=docker.for.mac.host.internal \
		--build-arg APP_ENV=$$APP_ENV

php-cs:
	docker-compose -f docker-compose.dev.yml exec -T -u www-data php php composer.phar run-script php-cs
