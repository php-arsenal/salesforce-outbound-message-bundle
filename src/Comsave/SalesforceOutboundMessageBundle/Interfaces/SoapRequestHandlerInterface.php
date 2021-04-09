<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Interfaces;

use PhpArsenal\SalesforceOutboundMessageBundle\Model\NotificationRequest;
use PhpArsenal\SalesforceOutboundMessageBundle\Model\NotificationResponse;

interface SoapRequestHandlerInterface
{
    public function notifications(NotificationRequest $notifications): NotificationResponse;
}