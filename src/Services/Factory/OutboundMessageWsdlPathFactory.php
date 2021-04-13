<?php

namespace PhpArsenal\SalesforceOutboundMessageBundle\Services\Factory;

use PhpArsenal\SalesforceOutboundMessageBundle\Exception\SalesforceException;
use PhpArsenal\SalesforceOutboundMessageBundle\Exception\WsdlFileNotFound;
use PhpArsenal\SalesforceOutboundMessageBundle\Interfaces\WsdlPathFactoryInterface;

class OutboundMessageWsdlPathFactory implements WsdlPathFactoryInterface
{
    /** @var array */
    private $abstractWsdlPaths;

    public function __construct(string $wsdlPath)
    {
        $this->abstractWsdlPaths = [
            rtrim($wsdlPath, '/'),
            realpath(dirname(__FILE__).'/../../Resources/wsdl'),
        ];
    }

    /**
     * @param string $objectName
     * @return string
     * @throws SalesforceException
     */
    public function getWsdlPath(string $objectName): string
    {
        foreach ($this->abstractWsdlPaths as $abstractWsdlPath) {
            $wsdlPath = $this->buildFullObjectWsdlPath($abstractWsdlPath, $objectName);

            if (file_exists($wsdlPath)) {
                return $wsdlPath;
            }
        }

        throw new WsdlFileNotFound($wsdlPath, $objectName);
    }

    private function buildFullObjectWsdlPath(string $abstractPath, string $objectName): string
    {
        return sprintf('%s/%s.wsdl', $abstractPath, $objectName);
    }
}