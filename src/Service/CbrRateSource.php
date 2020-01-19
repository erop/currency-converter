<?php


namespace App\Service;


use App\Entity\ExchangeRate;
use App\Exception\XmlException;
use DateTimeImmutable;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;

final class CbrRateSource extends AbstractRateSource
{
    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUrl(): string
    {
        return 'https://www.cbr.ru/scripts/XML_daily.asp';
    }

    protected function getDocumentDate(DOMDocument $doc): DateTimeImmutable
    {
        $xpath = new DOMXPath($doc);
        if (false === $nodeList = $xpath->query('/ValCurs/@Date')) {
            throw new XmlException('Malformed expression to find date element');
        }
        if (0 === $nodeList->length) {
            throw new XmlException('No nodes found in XML');
        }
        $dateAttr = $nodeList->item(0);
        if (null === $dateAttr) {
            throw new XmlException('No date attribute in XML');
        }
        return DateTimeImmutable::createFromFormat('d.m.Y', $dateAttr->nodeValue);
    }

    protected function getQuotes(DOMDocument $doc): DOMNodeList
    {
        $xpath = new DOMXPath($doc);
        return $xpath->query('Valute');
    }


    protected function createExchangeRate(DateTimeImmutable $date, DOMNode $node): ExchangeRate
    {
        $quoteCurrency = 'RUB';
        $template = <<<TMP
    <Valute ID="R01135">
        <NumCode>348</NumCode>
        <CharCode>HUF</CharCode>
        <Nominal>100</Nominal>
        <Name>Венгерских форинтов</Name>
        <Value>20,4423</Value>
    </Valute>
TMP;
        $xpath = new DOMXPath($node->ownerDocument);
        $quote = 1 / $xpath->query('Nominal', $node)->item(0)->nodeValue;
        $baseCurrency = $xpath->query('CharCode', $node)->item(0)->nodeValue;
        return new ExchangeRate($date, $baseCurrency, $quoteCurrency, $quote);
    }
}
