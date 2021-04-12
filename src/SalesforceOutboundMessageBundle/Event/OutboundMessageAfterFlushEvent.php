<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class OutboundMessageAfterFlushEvent extends Event
{
    const NAME = 'arsenal.salesforce_outbound_message.after_flush';

    private $document;

    public function getDocument()
    {
        return $this->document;
    }

    public function setDocument($document): void
    {
        $this->document = $document;
    }
}