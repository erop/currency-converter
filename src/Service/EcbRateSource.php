<?php


namespace App\Service;


use App\Contract\RateSourceInterface;
use App\Entity\ExchangeRate;

class EcbRateSource implements RateSourceInterface
{

    /**
     * EcbRateSource constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return ExchangeRate[]
     */
    public function getRates(): array
    {
        return [];
    }
}
