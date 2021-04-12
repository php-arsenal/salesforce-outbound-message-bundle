<?php

namespace Tests\Unit\Services\Factory;

use PhpArsenal\SalesforceOutboundMessageBundle\Services\Factory\OutboundMessageWsdlPathFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PhpArsenal\SalesforceOutboundMessageBundle\Services\Factory\OutboundMessageWsdlPathFactory
 */
class OutboundMessageWsdlPathFactoryTest extends TestCase
{
    /**
     * @var OutboundMessageWsdlPathFactory
     */
    protected $outboundMessageWsdlPathFactory;

    public function setUp(): void
    {
        $this->outboundMessageWsdlPathFactory = new OutboundMessageWsdlPathFactory('tests/Resources/wsdl/');
    }

    /**
     * @covers ::getWsdlPath()
     */
    public function testGetWsdlPathReturnsWsdlPathOnValidObjectName()
    {
        $objectName = 'DiscountRule__c';
        $wsdlPath = $this->outboundMessageWsdlPathFactory->getWsdlPath($objectName);

        $this->assertEquals('tests/Resources/wsdl/DiscountRule__c.wsdl', $wsdlPath);
    }

    /**
     * @covers ::getWsdlPath()
     * @expectedException \PhpArsenal\SalesforceOutboundMessageBundle\Exception\SalesforceException
     */
    public function testGetWsdlPathThrowsExceptionWhenFileCantBeFound()
    {
        $objectName = 'DoesNotExist';
        $this->outboundMessageWsdlPathFactory->getWsdlPath($objectName);
    }
}