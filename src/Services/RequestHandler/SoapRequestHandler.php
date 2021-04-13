<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Services\RequestHandler;

use PhpArsenal\SalesforceOutboundMessageBundle\Event\OutboundMessageAfterFlushEvent;
use PhpArsenal\SalesforceOutboundMessageBundle\Event\OutboundMessageBeforeFlushEvent;
use PhpArsenal\SalesforceOutboundMessageBundle\Exception\InvalidRequestException;
use PhpArsenal\SalesforceOutboundMessageBundle\Exception\SalesforceException;
use PhpArsenal\SalesforceOutboundMessageBundle\Interfaces\SoapRequestHandlerInterface;
use PhpArsenal\SalesforceOutboundMessageBundle\Model\NotificationRequest;
use PhpArsenal\SalesforceOutboundMessageBundle\Model\NotificationResponse;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageAfterFlushEventBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageBeforeFlushEventBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\DocumentUpdater;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\ObjectComparator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use PhpArsenal\SalesforceMapperBundle\Annotation\AnnotationReader;
use PhpArsenal\SalesforceMapperBundle\Annotation\Field;
use PhpArsenal\SalesforceMapperBundle\Mapper;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TypeError;

class SoapRequestHandler implements SoapRequestHandlerInterface
{
    /** @var DocumentManager */
    private $documentManager;

    /** @var Mapper */
    private $mapper;

    /** @var DocumentUpdater */
    private $documentUpdater;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var string
     */
    private $documentClassName;

    /** @var bool */
    private $isForceCompared;

    /** @var OutboundMessageBeforeFlushEventBuilder */
    private $outboundMessageBeforeFlushEventBuilder;

    /** @var OutboundMessageAfterFlushEventBuilder */
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
        string $documentClassName,
        bool $isForceCompared,
        OutboundMessageBeforeFlushEventBuilder $outboundMessageBeforeFlushEventBuilder,
        OutboundMessageAfterFlushEventBuilder $outboundMessageAfterFlushEventBuilder,
        ObjectComparator $objectComparator,
        AnnotationReader $salesforceAnnotationReader
    ) {
        $this->documentManager = $documentManager;
        $this->mapper = $mapper;
        $this->documentUpdater = $documentUpdater;
        $this->eventDispatcher = $eventDispatcher;
        $this->documentClassName = $documentClassName;
        $this->isForceCompared = $isForceCompared;
        $this->outboundMessageBeforeFlushEventBuilder = $outboundMessageBeforeFlushEventBuilder;
        $this->outboundMessageAfterFlushEventBuilder = $outboundMessageAfterFlushEventBuilder;
        $this->objectComparator = $objectComparator;
        $this->salesforceAnnotationReader = $salesforceAnnotationReader;
    }

    /**
     * @throws SalesforceException
     * @throws ReflectionException
     * @throws TypeError
     */
    public function notifications(NotificationRequest $request): NotificationResponse
    {
        $notifications = is_array($request->getNotification()) ? $request->getNotification() : [$request->getNotification()];

        foreach ($notifications as $notification) {
            $this->process($notification->sObject);
        }

        return (new NotificationResponse())->setAck(true);
    }

    /**
     * @throws SalesforceException
     * @throws ReflectionException
     * @throws TypeError
     */
    public function process($sObject)
    {
        if (!is_object($sObject)) {
            throw new InvalidRequestException();
        }

        $this->log('Document name: '.$this->documentClassName);
        $this->log('SoapRequestHandler: '.json_encode($sObject));

        $this->documentManager->clear($this->documentClassName);
        $this->mapper->getUnitOfWork()->clear();
        $mappedDocument = $this->mapper->mapToDomainObject($sObject, $this->documentClassName);
        $existingDocument = $this->documentManager->find($this->documentClassName, $mappedDocument->getId());

        $this->mapInitialDocumentValues($mappedDocument, $existingDocument);

        if ($this->isForceCompared
            && $existingDocument
            && $this->objectComparator->equals(
                $this->mapper->mapToSalesforceObject($mappedDocument),
                $this->mapper->mapToSalesforceObject($existingDocument)
            )) {
            $this->log('Objects are equal, skipping save');
            return;
        }

        $beforeFlushEvent = $this->outboundMessageBeforeFlushEventBuilder->build($mappedDocument, $existingDocument);
        $this->eventDispatcher->dispatch($beforeFlushEvent, OutboundMessageBeforeFlushEvent::NAME);

        if ($beforeFlushEvent->isSkipDocument()) {
            $this->log('Skipping save');
            return;
        }

        if ($existingDocument) {
            $this->log('saving existing');
            $this->documentUpdater->updateWithDocument($existingDocument, $mappedDocument);
        } else {
            $this->log('saving new');
            $this->documentManager->persist($mappedDocument);
            $existingDocument = $mappedDocument;
        }

        $this->documentManager->flush();

        $afterFlushEvent = $this->outboundMessageAfterFlushEventBuilder->build($existingDocument);
        $this->eventDispatcher->dispatch($afterFlushEvent, OutboundMessageAfterFlushEvent::NAME);
    }

    public function mapInitialDocumentValues($mappedDocument, $existingDocument): void
    {
        if($existingDocument) {
            $allowedProperties = $this->getAllowedProperties($this->documentClassName);
            $this->documentUpdater->updateWithDocument($mappedDocument, $existingDocument, null, $allowedProperties);
        }
    }

    public function getAllowedProperties(string $documentClass): array
    {
        /** @var Field[]|ArrayCollection|null $salesforceFields */
        $salesforceFields = $this->salesforceAnnotationReader->getSalesforceFields($documentClass);

        if(!$salesforceFields instanceof ArrayCollection) {
            return [];
        }

        return array_keys($salesforceFields->toArray());
    }

    public function log(string $message): void
    {
        if($this->logger) {
            $this->logger->debug(vsprintf('%s: %s', [
                __CLASS__,
                $message
            ]));
        }
    }

    /** @required */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}