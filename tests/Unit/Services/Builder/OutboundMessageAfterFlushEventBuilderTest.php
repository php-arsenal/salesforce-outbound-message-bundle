<?php

namespace Tests\Unit\Services\Builder;

use PhpArsenal\SalesforceOutboundMessageBundle\Event\OutboundMessageAfterFlushEvent;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageAfterFlushEventBuilder;
use PhpArsenal\SalesforceMapperBundle\Model\AbstractModel;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PhpArsenal\SalesforceOutboundMessageBundle\Services\Builder\OutboundMessageAfterFlushEventBuilder
 */
class OutboundMessageAfterFlushEventBuilderTest extends TestCase
{
    /**
     * @var OutboundMessageAfterFlushEventBuilder
     */
    private $outboundMessageAfterFlushEventBuilder;

    public function setUp(): void
    {
        $this->outboundMessageAfterFlushEventBuilder = new OutboundMessageAfterFlushEventBuilder();
    }

    /**
     * @covers ::build()
     */
    public function testBuild()
    {
        $this->createMock(OutboundMessageAfterFlushEvent::class);
        $documentMock = $this->createMock(AbstractModel::class);

        $afterFlushEventMock = $this->outboundMessageAfterFlushEventBuilder->build($documentMock);

        $this->assertInstanceOf(OutboundMessageAfterFlushEvent::class, $afterFlushEventMock);
        $this->assertEquals($documentMock, $afterFlushEventMock->getDocument());
    }
}