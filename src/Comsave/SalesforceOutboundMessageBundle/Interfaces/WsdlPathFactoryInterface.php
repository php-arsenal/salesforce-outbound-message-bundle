<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Interfaces;

interface WsdlPathFactoryInterface
{
    public function getWsdlPath(string $objectName): string;
}