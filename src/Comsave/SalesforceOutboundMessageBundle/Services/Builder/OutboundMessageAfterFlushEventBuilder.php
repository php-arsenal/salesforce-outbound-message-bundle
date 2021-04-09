<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder;

use PhpArsenal\SalesforceOutboundMessageBundle\Event\OutboundMessageAfterFlushEvent;

class OutboundMessageAfterFlushEventBuilder
{
    public function build($document)
    {
        $event = new OutboundMessageAfterFlushEvent();
        $event->setDocument($document);

        return $event;
    }
}