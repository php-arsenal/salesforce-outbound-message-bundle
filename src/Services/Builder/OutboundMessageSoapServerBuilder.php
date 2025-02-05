<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder;

use PhpArsenal\SalesforceOutboundMessageBundle\Exception\SalesforceException;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Factory\SalesforceObjectDocumentMetadataFactory;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Factory\OutboundMessageWsdlPathFactory;
use SoapServer;

class OutboundMessageSoapServerBuilder
{
    /** @var SoapServerBuilder */
    private $soapServerBuilder;

    /** @var OutboundMessageWsdlPathFactory */
    private $wsdlPathFactory;

    /**
     * @var SoapRequestHandlerBuilder
     */
    private $soapServerRequestHandlerBuilder;

    /**
     * @var SalesforceObjectDocumentMetadataFactory
     */
    private $salesforceObjectDocumentMetadataFactory;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        SoapServerBuilder $soapServerBuilder,
        OutboundMessageWsdlPathFactory $wsdlPathFactory,
        SoapRequestHandlerBuilder $soapServerRequestHandlerBuilder,
        SalesforceObjectDocumentMetadataFactory $salesforceObjectDocumentMetadataFactory
    ) {
        $this->soapServerBuilder = $soapServerBuilder;
        $this->wsdlPathFactory = $wsdlPathFactory;
        $this->soapServerRequestHandlerBuilder = $soapServerRequestHandlerBuilder;
        $this->salesforceObjectDocumentMetadataFactory = $salesforceObjectDocumentMetadataFactory;
    }

    /**
     * @param string $objectName
     * @return SoapServer
     * @throws SalesforceException
     */
    public function build(string $objectName): SoapServer
    {
        return $this->soapServerBuilder->build(
            $this->wsdlPathFactory->getWsdlPath($objectName),
            $this->soapServerRequestHandlerBuilder->build(
                $this->salesforceObjectDocumentMetadataFactory->getClassName($objectName),
                $this->salesforceObjectDocumentMetadataFactory->isForceCompared($objectName)
            )
        );
    }
}