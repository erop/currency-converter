<?php

namespace App\Tests\Unit\Service;

use App\Exception\XmlException;
use App\Service\EcbRateSource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class EcbRateSourceTest extends TestCase
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
<gesmes:Envelope xmlns:gesmes="http://www.gesmes.org/xml/2002-08-01" xmlns="http://www.ecb.int/vocabulary/2002-08-01/eurofxref">
	<gesmes:subject>Reference rates</gesmes:subject>
	<gesmes:Sender>
		<gesmes:name>European Central Bank</gesmes:name>
	</gesmes:Sender>
	<Cube>
		<Cube time='2020-01-17'>
			<Cube currency='USD' rate='1.1108'/>
			<Cube currency='JPY' rate='122.31'/>
			<Cube currency='BGN' rate='1.9558'/>
			<Cube currency='CZK' rate='25.147'/>
			<Cube currency='DKK' rate='7.4729'/>
			<Cube currency='GBP' rate='0.85105'/>
			<Cube currency='HUF' rate='335.59'/>
			<Cube currency='PLN' rate='4.2367'/>
			<Cube currency='RON' rate='4.7803'/>
			<Cube currency='SEK' rate='10.5450'/>
			<Cube currency='CHF' rate='1.0736'/>
			<Cube currency='ISK' rate='137.40'/>
			<Cube currency='NOK' rate='9.8890'/>
			<Cube currency='HRK' rate='7.4378'/>
			<Cube currency='RUB' rate='68.2495'/>
			<Cube currency='TRY' rate='6.5323'/>
			<Cube currency='AUD' rate='1.6122'/>
			<Cube currency='BRL' rate='4.6390'/>
			<Cube currency='CAD' rate='1.4498'/>
			<Cube currency='CNY' rate='7.6186'/>
			<Cube currency='HKD' rate='8.6292'/>
			<Cube currency='IDR' rate='15184.91'/>
			<Cube currency='ILS' rate='3.8372'/>
			<Cube currency='INR' rate='78.9567'/>
			<Cube currency='KRW' rate='1288.37'/>
			<Cube currency='MXN' rate='20.8338'/>
			<Cube currency='MYR' rate='4.5041'/>
			<Cube currency='NZD' rate='1.6782'/>
			<Cube currency='PHP' rate='56.548'/>
			<Cube currency='SGD' rate='1.4960'/>
			<Cube currency='THB' rate='33.746'/>
			<Cube currency='ZAR' rate='16.0582'/>
		</Cube>
	</Cube>
</gesmes:Envelope>
XML;
        $this->incorrectXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<gesmes:Envelope xmlns:gesmes="http://www.gesmes.org/xml/2002-08-01">
	<gesmes:subject>Reference rates</gesmes:subject>
	<gesmes:Sender>
		<gesmes:name>European Central Bank</gesmes:name>
	</gesmes:Sender>
	<Cube>
		<Cube time='2020-01-17'>
			<Cube currency='USD' rate='1.1108'/>
			<Cube currency='JPY' rate='122.31'/>
			<Cube currency='BGN' rate='1.9558'/>
			<Cube currency='CZK' rate='25.147'/>
			<Cube currency='DKK' rate='7.4729'/>
			<Cube currency='GBP' rate='0.85105'/>
			<Cube currency='HUF' rate='335.59'/>
			<Cube currency='PLN' rate='4.2367'/>
			<Cube currency='RON' rate='4.7803'/>
			<Cube currency='SEK' rate='10.5450'/>
			<Cube currency='CHF' rate='1.0736'/>
			<Cube currency='ISK' rate='137.40'/>
			<Cube currency='NOK' rate='9.8890'/>
			<Cube currency='HRK' rate='7.4378'/>
			<Cube currency='RUB' rate='68.2495'/>
			<Cube currency='TRY' rate='6.5323'/>
			<Cube currency='AUD' rate='1.6122'/>
			<Cube currency='BRL' rate='4.6390'/>
			<Cube currency='CAD' rate='1.4498'/>
			<Cube currency='CNY' rate='7.6186'/>
			<Cube currency='HKD' rate='8.6292'/>
			<Cube currency='IDR' rate='15184.91'/>
			<Cube currency='ILS' rate='3.8372'/>
			<Cube currency='INR' rate='78.9567'/>
			<Cube currency='KRW' rate='1288.37'/>
			<Cube currency='MXN' rate='20.8338'/>
			<Cube currency='MYR' rate='4.5041'/>
			<Cube currency='NZD' rate='1.6782'/>
			<Cube currency='PHP' rate='56.548'/>
			<Cube currency='SGD' rate='1.4960'/>
			<Cube currency='THB' rate='33.746'/>
			<Cube currency='ZAR' rate='16.0582'/>
		</Cube>
	</Cube>
</gesmes:Envelope>
XML;
    }

    public function testGivenCorrectXml(): void
    {
        $response = new MockResponse($this->correctXml);
        $client = new MockHttpClient([$response]);
        $source = new EcbRateSource($client);
        $rates = $source->getRates();
        $this->assertCount(32, $rates);
    }

    public function testGivenIncorrectXml(): void
    {
        $this->expectException(XmlException::class);
        $response = new MockResponse($this->incorrectXml);
        $client = new MockHttpClient([$response]);
        $source = new EcbRateSource($client);
        $source->getRates();
    }
}
