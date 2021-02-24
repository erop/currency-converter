### Task

Use https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml and https://www.cbr.ru/scripts/XML_daily.asp as data sources for acquiring currency rates for given date with console command.

All the data should be stored in DB using Doctrine.

Add configuration for switching data source (ECB or CBR). Implement currency conversion for currencies which are not the base currency for given bank.

Implement REST service for currency conversion.

Cover code with unit and integration tests.

The code should be built with Symfony 4.2.

### Before running the app

- make sure you have `Symfony CLI`, `Docker` and `Docker Compose` installed
  
- launch MySQL server with `docker-compose up -d --build`

- install packages with `symfony composer install`

- apply migration with `symfony console doctrine:migrations:migrate`

- check RATE_SOURCE in .env file: it should be either ECB or CBR
  
- execute `symfony console app:get-rates` to populate rates

- launch tests with `symfony php bin/phpunit` if you need to

- launch app with `symfony serve`, it should accept requests at https://127.0.0.1:8000

REST API will be available at `POST https://127.0.0.1:8000/exchange`, you should send body similar to:
````json
{
    "from_currency": "KRW",
    "to_currency": "JPY",
    "from_amount": "100"
}
````
