<?php


namespace App\Dto;


use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class ExchangeRequest
{
    /**
     * @var string
     * @Assert\Currency()
     * @Assert\NotBlank()
     * @SerializedName("from_currency")
     */
    private $fromCurrency;

    /**
     * @var string
     * @Assert\Currency()
     * @Assert\NotBlank()
     * @SerializedName("to_currency")
     */
    private $toCurrency;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Positive()
     * @SerializedName("from_amount")
     */
    private $fromAmount;

    /**
     * ExchangeRequest constructor.
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string $fromAmount
     */
    public function __construct(string $fromCurrency, string $toCurrency, string $fromAmount)
    {
        $this->fromCurrency = $fromCurrency;
        $this->toCurrency = $toCurrency;
        $this->fromAmount = $fromAmount;
    }

    /**
     * @return string
     */
    public function getFromCurrency(): string
    {
        return $this->fromCurrency;
    }

    /**
     * @return string
     */
    public function getToCurrency(): string
    {
        return $this->toCurrency;
    }

    /**
     * @return string
     */
    public function getFromAmount(): string
    {
        return $this->fromAmount;
    }

}
