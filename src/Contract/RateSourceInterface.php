<?php

namespace App\Contract;

use App\Entity\ExchangeRate;

interface RateSourceInterface
{
    /**
     * @return ExchangeRate[]
     */
    public function getRates(): array;
}
