### Task

Use https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml and https://www.cbr.ru/scripts/XML_daily.asp as data sources for acquiring currency rates for given date with console command.

All the data should be stored in DB using Doctrine.

Add configuration for switching data source (ECB or CBR). Implement currency conversion for currencies which are not the base currency for given bank.

Implement REST service for currency conversion.

Cover code with unit and integration tests.

The code should be built with Symfony 4.2.

### Before running the app

- set DATABASE_URL in .env

- check RATE_SOURCE в .env

- install dependencies `make build` 

- launch tests `make test`

- launch app `make run`

- execute `php ./bin/console app:get-rates` to populate rates

REST API will be available at `POST /exchange`, you should send body similar to:
````json
{
    "from_currency": "KRW",
    "to_currency": "JPY",
    "from_amount": "100"
}
````
