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
    private $baseCurrencyCode;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $quoteCurrencyCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rate;

    /**
     * ExchangeRate constructor.
     * @param $date
     * @param $baseCurrencyCode
     * @param $quoteCurrencyCode
     * @param $rate
     */
    public function __construct(
        DateTimeImmutable $date,
        string $baseCurrencyCode,
        string $quoteCurrencyCode,
        string $rate
    ) {
        $this->date = $date;
        $this->baseCurrencyCode = $baseCurrencyCode;
        $this->quoteCurrencyCode = $quoteCurrencyCode;
        $this->rate = $rate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBaseCurrencyCode(): string
    {
        return $this->baseCurrencyCode;
    }

    public function getQuoteCurrencyCode(): string
    {
        return $this->quoteCurrencyCode;
    }

    public function getTimestamp(): int
    {
        return $this->getDate()->getTimestamp();
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getRate(): string
    {
        return $this->rate;
    }
}
