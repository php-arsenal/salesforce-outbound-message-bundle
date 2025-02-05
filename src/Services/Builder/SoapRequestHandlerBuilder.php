<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder;

use PhpArsenal\SalesforceOutboundMessageBundle\Interfaces\SoapRequestHandlerInterface;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\DocumentUpdater;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\ObjectComparator;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\RequestHandler\SoapRequestHandler;
use Doctrine\ODM\MongoDB\DocumentManager;
use PhpArsenal\SalesforceMapperBundle\Annotation\AnnotationReader;
use PhpArsenal\SalesforceMapperBundle\Mapper;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SoapRequestHandlerBuilder
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var DocumentUpdater
     */
    private $documentUpdater;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var OutboundMessageBeforeFlushEventBuilder
     */
    private $outboundMessageBeforeFlushEventBuilder;

    /**
     * @var OutboundMessageAfterFlushEventBuilder
     */
    private $outboundMessageAfterFlushEventBuilder;

    /** @var ObjectComparator */
    private $objectComparator;

    /** @var AnnotationReader */
    private $salesforceAnnotationReader;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        DocumentManager $documentManager,
        Mapper $mapper,
        DocumentUpdater $documentUpdater,
        EventDispatcherInterface $eventDispatcher,
        OutboundMessageBeforeFlushEventBuilder $outboundMessageBeforeFlushEventBuilder,
        OutboundMessageAfterFlushEventBuilder $outboundMessageAfterFlushEventBuilder,
        ObjectComparator $objectComparator,
        AnnotationReader $salesforceAnnotationReader,
        LoggerInterface $logger
    ) {
        $this->documentManager = $documentManager;
        $this->mapper = $mapper;
        $this->documentUpdater = $documentUpdater;
        $this->eventDispatcher = $eventDispatcher;
        $this->outboundMessageBeforeFlushEventBuilder = $outboundMessageBeforeFlushEventBuilder;
        $this->outboundMessageAfterFlushEventBuilder = $outboundMessageAfterFlushEventBuilder;
        $this->objectComparator = $objectComparator;
        $this->salesforceAnnotationReader = $salesforceAnnotationReader;
        $this->logger = $logger;
    }

    public function build(string $documentName, bool $isForceCompared): SoapRequestHandlerInterface
    {
        $requestHandler = new SoapRequestHandler(
            $this->documentManager,
            $this->mapper,
            $this->documentUpdater,
            $this->eventDispatcher,
            $documentName,
            $isForceCompared,
            $this->outboundMessageBeforeFlushEventBuilder,
            $this->outboundMessageAfterFlushEventBuilder,
            $this->objectComparator,
            $this->salesforceAnnotationReader
        );

        $requestHandler->setLogger($this->logger);

        return $requestHandler;
    }
}