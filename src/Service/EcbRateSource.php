<?php


namespace App\Service;


use App\Entity\ExchangeRate;

class EcbRateSource extends AbstractRateSource
{
    /**
     * @return ExchangeRate[]
     */
    public function getRates(): array
    {
        return [];
    }
}
