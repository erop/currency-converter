<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ds\Hashable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExchangeRateRepository")
 */
class ExchangeRate implements Hashable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
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

    public function hash(): string
    {
        return sprintf(
            '%s%s%d',
            $this->getBaseCurrencyCode(),
            $this->getQuoteCurrencyCode(),
            $this->getTimestamp()
        );
    }

    public function getBaseCurrencyCode(): ?string
    {
        return $this->baseCurrencyCode;
    }

    public function getQuoteCurrencyCode(): ?string
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

    /**
     * @param ExchangeRate $obj
     * @inheritDoc
     */
    public function equals($obj): bool
    {
        if ($this === $obj) {
            return true;
        }
        return $this->getTimestamp() === $obj->getTimestamp()
            && $this->getBaseCurrencyCode() === $obj->getBaseCurrencyCode()
            && $this->getQuoteCurrencyCode() === $obj->getQuoteCurrencyCode()
            && $this->getRate() === $obj->getRate();
    }

    public function getRate(): string
    {
        return $this->rate;
    }
}
