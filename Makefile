.PHONY: build test run phpunit psalm

build:
	composer install

ok: phpunit psalm

test: phpunit

run:
	symfony serve

phpunit:
	./bin/phpunit

psalm:
	./vendor/bin/psalm
