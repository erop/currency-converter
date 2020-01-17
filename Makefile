.PHONY: build test run phpunit psalm

build:
	composer install

test: phpunit psalm

run:
	symfony serve

phpunit:
	./bin/phpunit

psalm:
	./vendor/bin/psalm
