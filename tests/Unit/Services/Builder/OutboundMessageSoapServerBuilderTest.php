<?php

namespace Tests\Unit\Services\Builder;

use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageSoapServerBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\SoapRequestHandlerBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\SoapServerBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Factory\SalesforceObjectDocumentMetadataFactory;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Factory\OutboundMessageWsdlPathFactory;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\RequestHandler\SoapRequestHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SoapServer;

/**
 * @coversDefaultClass \PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageSoapServerBuilder
 */
class OutboundMessageSoapServerBuilderTest extends TestCase
{
    /**
     * @var OutboundMessageSoapServerBuilder
     */
    protected $outboundMessageSoapServerBuilder;

    /**
     * @var MockObject|SoapServerBuilder
     */
    private $soapServerBuilderMock;

    /**
     * @var MockObject|OutboundMessageWsdlPathFactory
     */
    private $wsdlPathFactoryMock;

    /**
     * @var MockObject|SoapRequestHandlerBuilder
     */
    private $soapRequestHandlerBuilderMock;

    /**
     * @var MockObject|SalesforceObjectDocumentMetadataFactory
     */
    private $salesforceObjectDocumentMetadataFactory;

    public function setUp(): void
    {
        $this->soapServerBuilderMock = $this->createMock(SoapServerBuilder::class);
        $this->wsdlPathFactoryMock = $this->createMock(OutboundMessageWsdlPathFactory::class);
        $this->soapRequestHandlerBuilderMock = $this->createMock(SoapRequestHandlerBuilder::class);
        $this->salesforceObjectDocumentMetadataFactory = $this->createMock(SalesforceObjectDocumentMetadataFactory::class);
        $this->outboundMessageSoapServerBuilder = new OutboundMessageSoapServerBuilder(
            $this->soapServerBuilderMock,
            $this->wsdlPathFactoryMock,
            $this->soapRequestHandlerBuilderMock,
            $this->salesforceObjectDocumentMetadataFactory
        );
    }

    /**
     * @covers ::build()
     */
    public function testBuildReturnsASoapServer()
    {
        $this->wsdlPathFactoryMock->expects($this->once())
            ->method('getWsdlPath')
            ->willReturn('path/to/document.wsdl');

        $soapRequestHandler = $this->createMock(SoapRequestHandler::class);

        $this->soapRequestHandlerBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($soapRequestHandler);

        $soapServerMock = $this->createMock(SoapServer::class);

        $this->soapServerBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($soapServerMock);

        $objectName = 'Product';

        $this->salesforceObjectDocumentMetadataFactory->expects($this->once())
            ->method('getClassName')
            ->with($objectName)
            ->willReturn('DocumentClassPathName');

        $soapServer = $this->outboundMessageSoapServerBuilder->build($objectName);

        $this->assertEquals($soapServer, $soapServerMock);
    }
}
