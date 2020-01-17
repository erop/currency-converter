<?php


namespace App\Service;


use App\Entity\ExchangeRate;

class RateService
{
    /**
     * @var RateSourceFactory
     */
    private $factory;

    /**
     * RateService constructor.
     * @param RateSourceFactory $factory
     */
    public function __construct(RateSourceFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return ExchangeRate []
     */
    public function getRates(): array
    {
        $source = $this->factory->getRateSource();

        return [];
    }
}
