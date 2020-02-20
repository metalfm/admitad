include .env
export

all:
	echo "Use same command"

build:
	export DOCKER_BUILDKIT=1
	docker-compose -f docker-compose.dev.yml build \
		--build-arg APP_DEBUG=$$APP_DEBUG \
		--build-arg APP_DEBUG_ADDRESS=docker.for.mac.host.internal \
		--build-arg APP_ENV=$$APP_ENV
