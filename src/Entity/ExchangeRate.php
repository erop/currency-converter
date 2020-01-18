<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExchangeRateRepository")
 */
class ExchangeRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private $id;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $baseCurrency;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $quoteCurrency;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=8)
     */
    private $quote;

    /**
     * ExchangeRate constructor.
     * @param $date
     * @param $baseCurrencyCode
     * @param $quoteCurrencyCode
     * @param $quote
     */
    public function __construct(
        DateTimeImmutable $date,
        string $baseCurrencyCode,
        string $quoteCurrencyCode,
        string $quote
    ) {
        $this->date = $date;
        $this->baseCurrency = $baseCurrencyCode;
        $this->quoteCurrency = $quoteCurrencyCode;
        $this->quote = $quote;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function getQuoteCurrency(): string
    {
        return $this->quoteCurrency;
    }

    public function getTimestamp(): int
    {
        return $this->getDate()->getTimestamp();
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }
}
