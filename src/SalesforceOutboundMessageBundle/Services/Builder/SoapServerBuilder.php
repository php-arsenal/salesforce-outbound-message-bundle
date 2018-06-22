<?php

namespace SalesforceOutboundMessageBundle\Services\Builder;

use SalesforceOutboundMessageBundle\Model\NotificationRequest;
use SalesforceOutboundMessageBundle\Interfaces\SoapRequestHandlerInterface;

class SoapServerBuilder
{
    /**
     * @param string $wsdlPath
     * @param SoapRequestHandlerInterface $requestHandler
     * @return \SoapServer
     */
    public function build(string $wsdlPath, SoapRequestHandlerInterface $requestHandler): \SoapServer
    {
        $soapServer = new \SoapServer($wsdlPath, [
            'classmap' => [
                'notifications' => NotificationRequest::class,
            ],
            'encoding'=>'UTF-8'
        ]);

        $soapServer->setObject($requestHandler);

        return $soapServer;
    }
}