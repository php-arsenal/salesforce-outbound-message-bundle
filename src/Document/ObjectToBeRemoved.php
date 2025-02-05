<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Document;

use PhpArsenal\SalesforceOutboundMessageBundle\Interfaces\DocumentInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PhpArsenal\SalesforceMapperBundle\Annotation as Salesforce;

/**
 * @ODM\Document(collection="objectsToBeRemoved", repositoryClass="PhpArsenal\SalesforceOutboundMessageBundle\Repository\ObjectToBeRemovedRepository")
 * @Salesforce\SObject(name="ObjectToBeRemoved__c")
 */
class ObjectToBeRemoved implements DocumentInterface
{
    /**
     * @var string
     * @ODM\Id(strategy="NONE")
     * @Salesforce\Field(name="Id")
     */
    private $id;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Salesforce\Field(name="ObjectId__c")
     */
    private $objectId;

    /**
     * @var string
     * @ODM\Field(type="string")
     * @Salesforce\Field(name="ObjectClass__c")
     */
    private $objectClass;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function setObjectId(string $objectId): void
    {
        $this->objectId = $objectId;
    }

    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    public function setObjectClass(string $objectClass): void
    {
        $this->objectClass = $objectClass;
    }
}