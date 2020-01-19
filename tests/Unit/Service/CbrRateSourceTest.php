<?php

namespace App\Tests\Unit\Service;

use App\Exception\XmlException;
use App\Service\CbrRateSource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class CbrRateSourceTest extends TestCase
{
    /**
     * @var string
     */
    private $correctXml;
    /**
     * @var string
     */
    private $incorrectXml;

    public function setUp()
    {
        $this->correctXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<ValCurs Date="18.01.2020" name="Foreign Currency Market">
    <Valute ID="R01010">
        <NumCode>036</NumCode>
        <CharCode>AUD</CharCode>
        <Nominal>1</Nominal>
        <Name>Австралийский доллар</Name>
        <Value>42,5134</Value>
    </Valute>
    <Valute ID="R01020A">
        <NumCode>944</NumCode>
        <CharCode>AZN</CharCode>
        <Nominal>1</Nominal>
        <Name>Азербайджанский манат</Name>
        <Value>36,2707</Value>
    </Valute>
    <Valute ID="R01035">
        <NumCode>826</NumCode>
        <CharCode>GBP</CharCode>
        <Nominal>1</Nominal>
        <Name>Фунт стерлингов Соединенного королевства</Name>
        <Value>80,4917</Value>
    </Valute>
    <Valute ID="R01060">
        <NumCode>051</NumCode>
        <CharCode>AMD</CharCode>
        <Nominal>100</Nominal>
        <Name>Армянских драмов</Name>
        <Value>12,8261</Value>
    </Valute>
    <Valute ID="R01090B">
        <NumCode>933</NumCode>
        <CharCode>BYN</CharCode>
        <Nominal>1</Nominal>
        <Name>Белорусский рубль</Name>
        <Value>28,9814</Value>
    </Valute>
    <Valute ID="R01100">
        <NumCode>975</NumCode>
        <CharCode>BGN</CharCode>
        <Nominal>1</Nominal>
        <Name>Болгарский лев</Name>
        <Value>35,0358</Value>
    </Valute>
    <Valute ID="R01115">
        <NumCode>986</NumCode>
        <CharCode>BRL</CharCode>
        <Nominal>1</Nominal>
        <Name>Бразильский реал</Name>
        <Value>14,7029</Value>
    </Valute>
    <Valute ID="R01135">
        <NumCode>348</NumCode>
        <CharCode>HUF</CharCode>
        <Nominal>100</Nominal>
        <Name>Венгерских форинтов</Name>
        <Value>20,4423</Value>
    </Valute>
</ValCurs>
XML;
        $this->incorrectXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<ValCurs name="Foreign Currency Market">
    <Valute ID="R01010">
        <NumCode>036</NumCode>
        <CharCode>AUD</CharCode>
        <Nominal>1</Nominal>
        <Name>Австралийский доллар</Name>
        <Value>42,5134</Value>
    </Valute>
    <Valute ID="R01020A">
        <NumCode>944</NumCode>
        <CharCode>AZN</CharCode>
        <Nominal>1</Nominal>
        <Name>Азербайджанский манат</Name>
        <Value>36,2707</Value>
    </Valute>
    <Valute ID="R01035">
        <NumCode>826</NumCode>
        <CharCode>GBP</CharCode>
        <Nominal>1</Nominal>
        <Name>Фунт стерлингов Соединенного королевства</Name>
        <Value>80,4917</Value>
    </Valute>
    <Valute ID="R01060">
        <NumCode>051</NumCode>
        <CharCode>AMD</CharCode>
        <Nominal>100</Nominal>
        <Name>Армянских драмов</Name>
        <Value>12,8261</Value>
    </Valute>
    <Valute ID="R01090B">
        <NumCode>933</NumCode>
        <CharCode>BYN</CharCode>
        <Nominal>1</Nominal>
        <Name>Белорусский рубль</Name>
        <Value>28,9814</Value>
    </Valute>
    <Valute ID="R01100">
        <NumCode>975</NumCode>
        <CharCode>BGN</CharCode>
        <Nominal>1</Nominal>
        <Name>Болгарский лев</Name>
        <Value>35,0358</Value>
    </Valute>
    <Valute ID="R01115">
        <NumCode>986</NumCode>
        <CharCode>BRL</CharCode>
        <Nominal>1</Nominal>
        <Name>Бразильский реал</Name>
        <Value>14,7029</Value>
    </Valute>
    <Valute ID="R01135">
        <NumCode>348</NumCode>
        <CharCode>HUF</CharCode>
        <Nominal>100</Nominal>
        <Name>Венгерских форинтов</Name>
        <Value>20,4423</Value>
    </Valute>
</ValCurs>
XML;
    }

    public function testGivenCorrectXml(): void
    {
        $response = new MockResponse($this->correctXml);
        $client = new MockHttpClient([$response]);
        $source = new CbrRateSource($client);
        $rates = $source->getRates();
        $this->assertCount(8, $rates);
    }

    public function testGivenIncorrectXml(): void
    {
        $this->expectException(XmlException::class);
        $response = new MockResponse($this->incorrectXml);
        $client = new MockHttpClient([$response]);
        $source = new CbrRateSource($client);
        $source->getRates();
    }
}
