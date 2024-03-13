CURRENT_DIR:=$(dir $(abspath $(lastword $(MAKEFILE_LIST))))

include etc/make/variables.mk

# 🔝Main
.PHONY: default
default: info

.PHONY: info
info:
ifneq ($(OS),Windows_NT)
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z0-9_-]+:.*?##/ { printf "  \033[36m%-27s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
endif

.PHONY: build
build: deps start ## 🏗 Build app.

.PHONY: deps
deps: composer-install ## 🧩 Install Composer dependencies.

# 🐘 Composer
composer-env-file:
	@if [ ! -f .env.local ]; then echo '' > .env.local; fi

 .PHONY: composer-require
composer-require: DOCKER_COMMAND=require ## 🧩 Composer require.
composer-require: INTERACTIVE=-ti --interactive
composer-require: DOCKER_COMMAND_OPTIONS=--ignore-platform-reqs

 .PHONY: composer-update
composer-update: DOCKER_COMMAND=update ## 🧩 Composer update.
composer-update: INTERACTIVE=-ti --interactive
composer-update: DOCKER_COMMAND_OPTIONS=--ignore-platform-reqs

.PHONY: composer-require-dev
composer-require-dev: DOCKER_COMMAND=require --dev ## 🧩 Composer require dev.
composer-require-dev: INTERACTIVE=-ti --interactive
composer-require-dev: DOCKER_COMMAND_OPTIONS=--ignore-platform-reqs

.PHONY: composer-install
composer-install: DOCKER_COMMAND=install ## 🧩 Composer install.
composer-install: DOCKER_COMMAND_OPTIONS=--ignore-platform-reqs

.PHONY: composer-cache-clear
composer-cache-clear: DOCKER_COMMAND=run cache-clear ## 🧩 Symfony cache-clear.

.PHONY: composer
composer composer-install composer-require composer-require-dev composer-update: composer-env-file
	@docker run --rm $(INTERACTIVE) --volume $(CURRENT_DIR):/app --user $(id -u):$(id -g) \
		composer:2 $(DOCKER_COMMAND) \
			$(DOCKER_COMMAND_OPTIOM) \
			--no-ansi

# 🐳 Docker Compose
.PHONY: start
start: DOCKER_COMMAND=up --build -d ## ▶️ Up container.

.PHONY: stop
stop: DOCKER_COMMAND=stop ## ⏹ Stop container.

.PHONY: destroy
destroy: DOCKER_COMMAND=down

.PHONY: status
status:DOCKER_COMMAND=ps ## 📈 Containers status

# Usage: `make doco DOCKER_COMMAND="ps --services"`
# Usage: `make doco DOCKER_COMMAND="build --parallel --pull --force-rm --no-cache"`
.PHONY: doco
doco start stop destroy status: composer-env-file
	USER_ID=${shell id -u} GROUP_ID=${shell id -g} docker-compose $(DOCKER_COMMAND)

.PHONY: rebuild
rebuild: composer-env-file
	docker-compose build --pull --force-rm --no-cache
	make deps
	make start

# 🗄️ Database
.PHONY: init-db
init-db: ##  Init database from dump file base
init-db:DB_TARGET=app
init-db:DUMP_FILE=base_app

.PHONY: init-db-test
init-db-test: ##  init test database from dump file base
init-db-test:DB_TARGET=app_test
init-db-test:DUMP_FILE=base_app_test

.PHONY: restore-db
restore-db: ##  Restore database from dump file
restore-db:DB_TARGET=app
restore-db:DUMP_FILE=app

.PHONY: restore-db-test
restore-db-test: ##  Restore test database from dump file
restore-db-test:DB_TARGET=app_test
restore-db-test:DUMP_FILE=app_test

restore-db init-db restore-db-test init-db-test:
	@echo "${INFO_PROMPT_INIT}Clearing ${DB_TARGET} database...${INFO_PROMPT_END}"
	@docker exec database dropdb --if-exist -f ${DB_TARGET}
	@docker exec database createdb ${DB_TARGET}
	@echo "${INFO_PROMPT_INIT}Import ${DUMP_FILE}.dump data to ${DB_TARGET} database...${INFO_PROMPT_END}"
	@docker exec -i database pg_restore --format c --dbname ${DB_TARGET} < etc$(PSEP)postgres$(PSEP)${DUMP_FILE}.dump

.PHONY: db-dump
db-dump: ##  Dumps current database in predefined location
db-dump:DB_TARGET=app

.PHONY: db-dump-test
db-dump-test: ##  Dumps current test database in predefined location
db-dump-test:DB_TARGET=app_test

db-dump db-dump-test:
	@echo "${INFO_PROMPT_INIT}Dumping ${DB_TARGET} database into ${DB_TARGET}.dump...${INFO_PROMPT_END}"
	@docker exec database pg_dump --format c --clean --create ${DB_TARGET} > etc$(PSEP)postgres$(PSEP)${DB_TARGET}.dump
	@echo "Done!"

.PHONY: doctrine-migrate-db
doctrine-migrate-db: ##  Execute migrations on database
doctrine-migrate-db:DB_TARGET=app

.PHONY: doctrine-migrate-db-test
doctrine-migrate-db-test: ##  Execute migrations on test database
doctrine-migrate-db-test:DB_TARGET=app_test
doctrine-migrate-db-test:ENV_TARGET=--env=test

doctrine-migrate-db doctrine-migrate-db-test:
	@echo "${INFO_PROMPT_INIT}Migrate ${DB_TARGET} database...${INFO_PROMPT_END}"
	@docker exec -t api bin/head doctrine:migrations:migrate ${ENV_TARGET} --no-interaction

# ✅ Tests
.PHONY: u-tests
u-tests: composer-env-file ## ✅  Unit tests
	@echo "${INFO_PROMPT_INIT}Run unit tests...${INFO_PROMPT_END}"
	@docker exec api ./vendor/bin/phpunit --colors=always --group unit

.PHONY: i-tests
i-tests: composer-env-file ## ✅  Integration tests
	@echo "${INFO_PROMPT_INIT}Run integration tests...${INFO_PROMPT_END}"
	@docker exec api ./vendor/bin/phpunit --colors=always --group integration

.PHONY: f-tests
f-tests: composer-env-file ## ✅  Functionality tests
	@echo "${INFO_PROMPT_INIT}Run functionality tests...${INFO_PROMPT_END}"
	@docker exec api ./vendor/bin/phpunit --colors=always --group functionality

.PHONY: a-tests
a-tests: composer-env-file ## ✅  Acceptance tests
	@echo "${INFO_PROMPT_INIT}Run acceptance tests...${INFO_PROMPT_END}"
	@docker exec api ./vendor/bin/behat --colors --format=progress -v

.PHONY: tests
tests: composer-env-file u-tests i-tests f-tests a-tests ## ✅  All tests
##  init-db-test doctrine-migrate-db-test

# ⚒️ Utils
.PHONY: cache-clear
cache-clear: ##   Clears symfony cache
	@echo "${INFO_PROMPT_INIT}Clearing cache...${INFO_PROMPT_END}"
	@docker run --rm $(INTERACTIVE) --volume $(CURRENT_DIR):/app --user $(id -u):$(id -g) \
		composer:2 run post-install-cmd

.PHONY: enable-xdebug
xdebug-enable: ## 🧰 Enable xDebug
	@echo "${INFO_PROMPT_INIT}Enabling xdebug...${INFO_PROMPT_END}"
	@docker exec -u 0 api sh -c "sed -i 's|xdebug.mode = .*|xdebug.mode = develop,debug|g' /usr/local/etc/php/conf.d/xdebug.ini"
	@docker exec -u 0 api sh -c "sed -i 's|xdebug.start_with_request = .*|xdebug.start_with_request = yes|g' /usr/local/etc/php/conf.d/xdebug.ini"
	@echo "${INFO_PROMPT_INIT}Fixing xdebug...${INFO_PROMPT_END}"
	@docker exec -u 0 api sh -c "sed -i 's|xdebug.client_host = .*|xdebug.client_host = host.docker.internal|g' /usr/local/etc/php/conf.d/xdebug.ini"
	@docker exec -u 0 api sh -c "cat /usr/local/etc/php/conf.d/xdebug.ini"
	@echo "${INFO_PROMPT_INIT}Restarting xdebug on...${INFO_PROMPT_END}"
	@$(MAKE) stop
	@$(MAKE) start

.PHONY: xdebug-disable
xdebug-disable: ## 📴 Disable xDebug
	@echo "${INFO_PROMPT_INIT}Disabling xdebug...${INFO_PROMPT_END}"
	@docker exec -u 0 api sh -c "sed -i 's|xdebug.mode = .*|xdebug.mode = off|g' /usr/local/etc/php/conf.d/xdebug.ini"
	@docker exec -u 0 api sh -c "sed -i 's|xdebug.start_with_request = .*|xdebug.start_with_request = no|g' /usr/local/etc/php/conf.d/xdebug.ini"
	@docker exec -u 0 api sh -c "cat /usr/local/etc/php/conf.d/xdebug.ini"
	@echo "${INFO_PROMPT_INIT}Restarting xdebug off...${INFO_PROMPT_END}"
	@$(MAKE) stop
	@$(MAKE) start

.PHONY: phpstan
phpstan: ## 📊 PHPStan (make psalm PHPSTAN_OPTIONS="--help")
	@echo "${INFO_PROMPT_INIT}Run PHPStan static code analysis...${INFO_PROMPT_END}"
	@docker exec api ./vendor/bin/phpstan analyse --no-progress ${PHPSTAN_OPTIONS}

.PHONY: psalm
psalm: ## 📊 Psalm (make psalm PSALM_OPTIONS="--help")
	@echo "${INFO_PROMPT_INIT}Run Psalm static code analysis...${INFO_PROMPT_END}"
	@docker exec api ./vendor/bin/psalm --no-progress ${PSALM_OPTIONS}

.PHONY: code-static-analyse
code-static-analyse: phpstan psalm ## 📊 Code static analysis with PHPStan and PSalm

.PHONY: shell-api
shell-api: ## 💻 api shell
	@docker exec -it api sh
