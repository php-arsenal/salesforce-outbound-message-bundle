<?php

namespace Tests\Unit\DependencyInjection;

use PhpArsenal\SalesforceOutboundMessageBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PhpArsenal\SalesforceOutboundMessageBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends TestCase
{
    /**
     * @covers ::getConfigTreeBuilder()
     */
    public function testConfiguration(): void
    {
        $inputOutput = [
            'salesforce_outbound_message' => [
                'wsdl_cache' => 'WSDL_CACHE_NONE',
                'wsdl_directory' => '%kernel.project_dir%/Resources/wsdl_documents',
                'document_paths' => [
                    'ObjectToBeRemoved__c' => [
                        'path' => 'PhpArsenal\SalesforceOutboundMessageBundle\Document\ObjectToBeRemoved',
                    ],
                ],
            ]
        ];

        $configuration = new Configuration();

        $configNode = $configuration->getConfigTreeBuilder()->buildTree();
        $resultConfig = $configNode->finalize($configNode->normalize($inputOutput));

        $this->assertEquals($inputOutput, $resultConfig);
    }
}