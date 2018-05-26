all: coverage-text

install-dev:
	composer install --dev

coverage-text:
	./vendor/bin/phpunit --coverage-text tests/wp-robots-noindex

coverage-html:
	./vendor/bin/phpunit tests/wp-robots-noindex --coverage-html coverage

