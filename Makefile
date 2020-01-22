.PHONY: build test run

build:
	composer install

test:
	./bin/phpunit

run:
	symfony serve

