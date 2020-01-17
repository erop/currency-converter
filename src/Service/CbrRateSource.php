<?php


namespace App\Service;


use App\Contract\RateSourceInterface;
use App\Entity\ExchangeRate;

class CbrRateSource implements RateSourceInterface
{

    /**
     * @return ExchangeRate[]
     */
    public function getRates(): array
    {
        return [];
    }
}
