<?php


namespace App\Service;


use App\Dto\ExchangeResponse;
use App\Entity\ExchangeRate;
use App\Message\Query\GetCurrencyConverted;
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

    public function process(GetCurrencyConverted $query): ?ExchangeResponse
    {
        $fromCurrency = $query->getFromCurrency();
        $toCurrency = $query->getToCurrency();
        $fromAmount = $query->getFromAmount();
        $scale = SymfonyCurrencies::getFractionDigits($toCurrency);

        // try to use direct currency pair
        $rate = $this->repository->findOneBy(['baseCurrency' => $fromCurrency, 'quoteCurrency' => $toCurrency]);
        if ($rate) {
            $toAmount = $this->calculateDirectConversion($fromAmount, $rate, $scale);
            return new ExchangeResponse($fromCurrency, $toCurrency, $fromAmount, $toAmount);
        }

        // try to use inverted currency pair
        $rate = $this->repository->findOneBy(['baseCurrency' => $toCurrency, 'quoteCurrency' => $fromCurrency]);
        if ($rate) {
            $toAmount = $this->calculateInvertedConversion($fromAmount, $rate, $scale);
            return new ExchangeResponse($fromCurrency, $toCurrency, $fromAmount, $toAmount);
        }

        // try to use adjacent currency pairs assuming they have the same base currency
        $invertedRate = $this->repository->findOneBy(['quoteCurrency' => $fromCurrency]);
        $directRate = $this->repository->findOneBy(['quoteCurrency' => $toCurrency]);
        if (null !== $invertedRate
            && null !== $directRate
            && $invertedRate->getBaseCurrency() === $directRate->getBaseCurrency()
        ) {
            $invertedScale = SymfonyCurrencies::getFractionDigits($invertedRate->getBaseCurrency());
            $directScale = SymfonyCurrencies::getFractionDigits($directRate->getQuoteCurrency());
            $intermediateAmount = $this->calculateInvertedConversion($fromAmount, $invertedRate, $invertedScale);
            $toAmount = $this->calculateDirectConversion($intermediateAmount, $directRate, $directScale);
            return new ExchangeResponse($fromCurrency, $toCurrency, $fromAmount, $toAmount);
        }

        // todo add custom validator to avoid that $fromCurrency will be equal to $toCurrency

        // todo make RUB base currency for CBR rate source

        // todo add third logic for calculating simple (only two edges) adjacent pairs

        // todo use serious graph algorithm to fuck this shit in a supa-pupa way

        return null;
    }

    /**
     * @param string $fromAmount
     * @param ExchangeRate $rate
     * @param int $scale
     * @return string
     */
    protected function calculateDirectConversion(
        string $fromAmount,
        ExchangeRate $rate,
        int $scale
    ): string {
        return number_format(
            round(
                bcmul($fromAmount, $rate->getQuote(), $scale + 1), $scale
            ),
            $scale
        );
    }

    /**
     * @param string $fromAmount
     * @param ExchangeRate $rate
     * @param int $scale
     * @return string
     */
    protected function calculateInvertedConversion(string $fromAmount, ExchangeRate $rate, int $scale): string
    {
        return number_format(
            round(
                bcdiv($fromAmount, $rate->getQuote(), $scale + 1), $scale
            ),
            $scale
        );
    }
}
