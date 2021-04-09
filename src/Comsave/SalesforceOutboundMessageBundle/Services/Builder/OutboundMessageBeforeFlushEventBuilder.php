<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder;

use PhpArsenal\SalesforceOutboundMessageBundle\Event\OutboundMessageBeforeFlushEvent;

class OutboundMessageBeforeFlushEventBuilder
{
    public function build($newDocument, $existingDocument)
    {
        $event = new OutboundMessageBeforeFlushEvent();
        $event->setNewDocument($newDocument);
        $event->setExistingDocument($existingDocument);

        return $event;
    }
}