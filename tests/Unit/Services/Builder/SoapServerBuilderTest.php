<?php

namespace Tests\Unit\Services\Builder;

use PhpArsenal\SalesforceOutboundMessageBundle\Interfaces\SoapRequestHandlerInterface;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\SoapServerBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\RequestHandler\SoapRequestHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SoapServer;

/**
 * @coversDefaultClass \PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\SoapServerBuilder
 */
class SoapServerBuilderTest extends TestCase
{
    /** @var SoapServerBuilder|MockObject */
    protected $soapServerBuilder;

    public function setUp(): void
    {
        $wsdlCache = 'WSDL_CACHE_DISK';
        $this->soapServerBuilder = new SoapServerBuilder($wsdlCache);
    }

    /**
     * @covers ::build()
     */
    public function testBuildReturnsASoapServer()
    {
        $wsdlPath = 'tests/Resources/wsdl/DiscountRule__c.wsdl';
        /** @var SoapRequestHandler|MockObject $soapRequestHandler */
        $soapRequestHandler = $this->createMock(SoapRequestHandlerInterface::class);

        $soapServer = $this->soapServerBuilder->build($wsdlPath, $soapRequestHandler);

        $this->assertInstanceOf(SoapServer::class, $soapServer);
    }
}
