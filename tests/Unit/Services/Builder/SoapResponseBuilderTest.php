<?php

namespace Tests\Unit\Services\Builder;

use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\SoapResponseBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversDefaultClass \PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\SoapResponseBuilder
 */
class SoapResponseBuilderTest extends TestCase
{
    /**
     * @var SoapResponseBuilder
     */
    protected $soapResponseBuilder;

    public function setUp(): void
    {
        $this->soapResponseBuilder = new SoapResponseBuilder();
    }

    /**
     * @covers ::build()
     */
    public function testBuildReturnsAResponse()
    {
        $response = $this->soapResponseBuilder->build('Request successful');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Request successful', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/xml; charset=ISO-8859-1', $response->headers->get('content-type'));
    }
}