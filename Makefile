# Quality gates, run in the PHP container. Same order as the CI: cheapest checks first.
.DEFAULT_GOAL := help

DC       := docker compose
# Run as the host user so that vendor/ and var/ stay owned by you (Linux).
PHP      := $(DC) run --rm --no-deps --user $(shell id -u):$(shell id -g) -e HOME=/tmp php
EXAMPLES := $(patsubst %/composer.json,%,$(wildcard exemples/*/composer.json))

.PHONY: help up down install qa qa-project qa-examples cs-fix

help: ## Show the available commands
	@grep -E '^[a-z-]+:.*## ' $(MAKEFILE_LIST) | awk -F':.*## ' '{printf "  %-12s %s\n", $$1, $$2}'

up: ## Start the application on http://localhost:8000
	$(DC) up -d --build

down: ## Stop the application
	$(DC) down

install: ## Install the dependencies of the project and of every example
	$(PHP) composer install --no-interaction --no-progress
	@for dir in $(EXAMPLES); do $(PHP) sh -c "cd /app/$$dir && composer install --no-interaction --no-progress" || exit 1; done

qa: install qa-project qa-examples ## Run every quality gate
	@echo "All quality gates passed."

qa-project: ## Quality gates of the Tooloc project
	$(PHP) composer validate --strict
	$(PHP) composer audit
	$(PHP) vendor/bin/php-cs-fixer check
	$(PHP) bin/console cache:warmup --env=dev --quiet
	$(PHP) vendor/bin/phpstan analyse --no-progress
	$(PHP) vendor/bin/deptrac analyse --no-progress
	$(PHP) bin/phpunit

qa-examples: ## Quality gates of the isolated examples
	@for dir in $(EXAMPLES); do \
		echo "== $$dir"; \
		$(PHP) sh -c "cd /app/$$dir \
			&& composer validate --strict \
			&& composer audit \
			&& vendor/bin/php-cs-fixer check \
			&& vendor/bin/phpstan analyse --no-progress \
			&& vendor/bin/deptrac analyse --no-progress \
			&& vendor/bin/phpunit \
			&& vendor/bin/infection --threads=max --no-progress --show-mutations=0" || exit 1; \
	done

cs-fix: ## Fix the coding style of the project and of every example
	$(PHP) vendor/bin/php-cs-fixer fix
	@for dir in $(EXAMPLES); do $(PHP) sh -c "cd /app/$$dir && vendor/bin/php-cs-fixer fix" || exit 1; done
