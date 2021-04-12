<?php

namespace Tests\Functional\Services\RequestHandler;

use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageAfterFlushEventBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageBeforeFlushEventBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\DocumentUpdater;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\ObjectComparator;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\RequestHandler\SoapRequestHandler;
use Doctrine\Common\Cache\Cache;
use Doctrine\ODM\MongoDB\DocumentManager;
use PhpArsenal\SalesforceMapperBundle\Annotation\AnnotationReader;
use PhpArsenal\SalesforceMapperBundle\Mapper;
use PhpArsenal\SoapClient\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\Stub\DocumentWithSalesforceFields;

/**
 * @coversDefaultClass \PhpArsenal\SalesforceOutboundMessageBundle\Services\RequestHandler\SoapRequestHandler
 */
class SoapRequestHandlerTest extends TestCase
{
    /**
     * @var SoapRequestHandler
     */
    protected $soapRequestHandler;

    /**
     * @var MockObject|DocumentManager
     */
    private $documentManagerMock;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var MockObject|DocumentUpdater
     */
    private $documentUpdaterMock;

    /**
     * @var MockObject|EventDispatcherInterface
     */
    private $eventDispatcherMock;

    /**
     * @var MockObject|OutboundMessageBeforeFlushEventBuilder
     */
    private $outboundMessageBeforeFlushEventBuilderMock;

    /**
     * @var MockObject|OutboundMessageAfterFlushEventBuilder
     */
    private $outboundMessageAfterFlushEventBuilderMock;

    /** @var ObjectComparator|MockObject */
    private $objectComparatorMock;

    /** @var AnnotationReader */
    private $salesforceAnnotationReader;

    public function setUp(): void
    {
        $this->documentManagerMock = $this->createMock(DocumentManager::class);
        $this->documentUpdaterMock = $this->createMock(DocumentUpdater::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->outboundMessageBeforeFlushEventBuilderMock = $this->createMock(OutboundMessageBeforeFlushEventBuilder::class);
        $this->outboundMessageAfterFlushEventBuilderMock = $this->createMock(OutboundMessageAfterFlushEventBuilder::class);
        $this->objectComparatorMock = $this->createMock(ObjectComparator::class);

        $this->salesforceAnnotationReader = new AnnotationReader(new \Doctrine\Common\Annotations\AnnotationReader());
        $this->mapper = new Mapper(
            $this->createMock(Client::class),
            $this->salesforceAnnotationReader,
            $this->createMock(Cache::class)
        );

        $this->soapRequestHandler = new SoapRequestHandler(
            $this->documentManagerMock,
            $this->mapper,
            $this->documentUpdaterMock,
            $this->eventDispatcherMock,
            'Product2',
            false,
            $this->outboundMessageBeforeFlushEventBuilderMock,
            $this->outboundMessageAfterFlushEventBuilderMock,
            $this->objectComparatorMock,
            $this->salesforceAnnotationReader
        );
    }

    /** @covers ::getAllowedProperties */
    public function testGetAllowedProperties(): void
    {
        $this->assertEquals([
            'someField',
            'someOtherField'
        ], $this->soapRequestHandler->getAllowedProperties(DocumentWithSalesforceFields::class));
    }
}