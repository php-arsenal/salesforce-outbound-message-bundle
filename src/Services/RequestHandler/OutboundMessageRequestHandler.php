<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Services\RequestHandler;

use PhpArsenal\SalesforceOutboundMessageBundle\Exception\SalesforceException;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageSoapServerBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\SoapResponseBuilder;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Resolver\OutboundMessageObjectNameResolver;
use Symfony\Component\HttpFoundation\Response;

class OutboundMessageRequestHandler
{
    /**
     * @var OutboundMessageSoapServerBuilder
     */
    private $outboundMessageSoapServerBuilder;

    /**
     * @var SoapResponseBuilder
     */
    private $soapServerResponseBuilder;

    /**
     * @var OutboundMessageObjectNameResolver
     */
    private $outboundMessageObjectNameResolver;

    /**
     * OutboundMessageRequestHandler constructor.
     * @param OutboundMessageSoapServerBuilder $outboundMessageSoapServerBuilder
     * @param SoapResponseBuilder $soapServerResponseBuilder
     * @param OutboundMessageObjectNameResolver $outboundMessageObjectNameResolver
     * @codeCoverageIgnore
     */
    public function __construct(
        OutboundMessageSoapServerBuilder $outboundMessageSoapServerBuilder,
        SoapResponseBuilder $soapServerResponseBuilder,
        OutboundMessageObjectNameResolver $outboundMessageObjectNameResolver
    ) {
        $this->outboundMessageSoapServerBuilder = $outboundMessageSoapServerBuilder;
        $this->soapServerResponseBuilder = $soapServerResponseBuilder;
        $this->outboundMessageObjectNameResolver = $outboundMessageObjectNameResolver;
    }

    /**
     * @param string $xml
     * @return Response
     * @throws SalesforceException
     */
    public function handle(string $xml): Response
    {
        $objectName = $this->outboundMessageObjectNameResolver->resolve($xml);
        $soapServer = $this->outboundMessageSoapServerBuilder->build($objectName);

        ob_start('salesforce_outbound_message_bundle_soap_server_output_buffer_handler');
        $soapServer->handle($xml);
        $responseContent = ob_get_contents();
        ob_end_clean();

        return $this->soapServerResponseBuilder->build($responseContent);
    }
}