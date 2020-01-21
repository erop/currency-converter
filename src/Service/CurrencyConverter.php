<?php


namespace App\Service;


use App\Message\Query\GetCurrencyConverted;
use App\Dto\ExchangeResponse;
use App\Repository\ExchangeRateRepository;
use Symfony\Component\Intl\Currencies as SymfonyCurrencies;

class CurrencyConverter
{
    /**
     * @var ExchangeRateRepository
     */
    private $repository;

    /**
     * CurrencyExchanger constructor.
     * @param $repository
     */
    public function __construct(ExchangeRateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function process(GetCurrencyConverted $request): ?ExchangeResponse
    {
        $fromCurrency = $request->getFromCurrency();
        $toCurrency = $request->getToCurrency();
        $fromAmount = $request->getFromAmount();
        $scale = SymfonyCurrencies::getFractionDigits($toCurrency);

        // try to use direct currency pair
        $rate = $this->repository->findOneBy(['baseCurrency' => $fromCurrency, 'quoteCurrency' => $toCurrency]);
        if ($rate) {
            $toAmount = number_format(
                round(
                    bcmul($fromAmount, $rate->getQuote(), $scale + 1), $scale
                ),
                $scale
            );
            return new ExchangeResponse($fromCurrency, $toCurrency, $fromAmount, $toAmount);
        }

        // try to use inverted currency pair
        $rate = $this->repository->findOneBy(['baseCurrency' => $toCurrency, 'quoteCurrency' => $fromCurrency]);
        if ($rate) {
            $toAmount = number_format(
                round(
                    bcdiv($fromAmount, $rate->getQuote(), $scale + 1), $scale
                ),
                $scale
            );
            return new ExchangeResponse($fromCurrency, $toCurrency, $fromAmount, $toAmount);
        }


        return null;
    }
}
