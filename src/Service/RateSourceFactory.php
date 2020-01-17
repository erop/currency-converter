<?php


namespace App\Service;


use App\Contract\RateSourceInterface;

class RateSourceFactory
{
    /**
     * @var string
     */
    private $rateSource;


    /**
     * RateSourceFactory constructor.
     * @param string $rateSource
     */
    public function __construct(string $rateSource)
    {
        $this->rateSource = $rateSource;
    }

    public function getRateSource(): RateSourceInterface
    {
        switch ($this->rateSource) {
            case 'ECB':
                return new EcbRateSource();
            case 'CBR':
                return new CbrRateSource();
        }
    }
}
