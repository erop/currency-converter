<?php


namespace App\Service;


use App\Entity\ExchangeRate;

final class CbrRateSource extends AbstractRateSource
{
    /**
     * @return ExchangeRate[]
     */
    public function getRates(): array
    {
        return [];
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUrl(): string
    {
        return 'https://www.cbr.ru/scripts/XML_daily.asp';
    }
}
