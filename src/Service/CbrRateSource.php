<?php


namespace App\Service;


use App\Entity\ExchangeRate;

class CbrRateSource extends AbstractRateSource
{
    /**
     * @return ExchangeRate[]
     */
    public function getRates(): array
    {
        return [];
    }
}
