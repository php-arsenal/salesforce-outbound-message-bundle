<?php

namespace Tests\Unit\Services;

use PhpArsenal\SalesforceOutboundMessageBundle\Exception\SalesforceException;
use PhpArsenal\SalesforceOutboundMessageBundle\Services\Resolver\OutboundMessageObjectNameResolver;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \PhpArsenal\SalesforceOutboundMessageBundle\Services\Resolver\OutboundMessageObjectNameResolver
 */
class OutboundMessageObjectNameRetrieverTest extends TestCase
{
    /**
     * @var OutboundMessageObjectNameResolver
     */
    protected $outboundMessageObjectNameResolver;

    public function setUp(): void
    {
        $this->outboundMessageObjectNameResolver = new OutboundMessageObjectNameResolver();
    }

    /**
     * @covers ::resolve()
     */
    public function testRetrieveReturnsObjectNameOnValidXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                 <soapenv:Body>
                  <notifications xmlns="http://soap.sforce.com/2005/09/outbound">
                   <OrganizationId>00DD0000000muCUMAY</OrganizationId>
                   <ActionId>04k57000000TOUIAA4</ActionId>
                   <SessionId xsi:nil="true"/>
                   <EnterpriseUrl>https://eu4.salesforce.com/services/Soap/c/42.0/00DD0000000muCU</EnterpriseUrl>
                   <PartnerUrl>https://eu4.salesforce.com/services/Soap/u/42.0/00DD0000000muCU</PartnerUrl>
                   <Notification>
                    <Id>04l570000176YSmAAM</Id>
                    <sObject xsi:type="sf:DiscountRule__c" xmlns:sf="urn:sobject.enterprise.soap.sforce.com">
                     <sf:Id>a025700000OPgOjAAL</sf:Id>
                     <sf:ContractTerm__c>24</sf:ContractTerm__c>
                     <sf:CreatedDate>2017-05-02T11:53:53.000Z</sf:CreatedDate>
                     <sf:Discount__c>0.0</sf:Discount__c>
                     <sf:IsDeleted>false</sf:IsDeleted>
                     <sf:LastModifiedDate>2018-05-04T11:35:40.000Z</sf:LastModifiedDate>
                     <sf:Name>244 Months</sf:Name>
                     <sf:Price__c>419.0</sf:Price__c>
                     <sf:Product__c>01t57000006qVLRAA2</sf:Product__c>
                     <sf:Type__c>Wholesale</sf:Type__c>
                    </sObject>
                   </Notification>
                  </notifications>
                 </soapenv:Body>
                </soapenv:Envelope>';

        $objectName = $this->outboundMessageObjectNameResolver->resolve($xml);

        $this->assertEquals('DiscountRule__c', $objectName);
    }

    /**
     * @covers ::resolve()
     */
    public function testRetrieveThrowsExceptionOnInvalidXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                 <soapenv:Body>
                  <notifications xmlns="http://soap.sforce.com/2005/09/outbound">
                   <OrganizationId>00DD0000000muCUMAY</OrganizationId>
                   <ActionId>04k57000000TOUIAA4</ActionId>
                   <SessionId xsi:nil="true"/>
                   <EnterpriseUrl>https://eu4.salesforce.com/services/Soap/c/42.0/00DD0000000muCU</EnterpriseUrl>
                   <PartnerUrl>https://eu4.salesforce.com/services/Soap/u/42.0/00DD0000000muCU</PartnerUrl>
                   <Notification>
                    <Id>04l570000176YSmAAM</Id>
                     <sf:Id>a025700000OPgOjAAL</sf:Id>
                     <sf:ContractTerm__c>24</sf:ContractTerm__c>
                     <sf:CreatedDate>2017-05-02T11:53:53.000Z</sf:CreatedDate>
                     <sf:Discount__c>0.0</sf:Discount__c>
                     <sf:IsDeleted>false</sf:IsDeleted>
                     <sf:LastModifiedDate>2018-05-04T11:35:40.000Z</sf:LastModifiedDate>
                     <sf:Name>244 Months</sf:Name>
                     <sf:Price__c>419.0</sf:Price__c>
                     <sf:Product__c>01t57000006qVLRAA2</sf:Product__c>
                     <sf:Type__c>Wholesale</sf:Type__c>
                    </sObject>
                   </Notification>
                  </notifications>
                 </soapenv:Body>
                </soapenv:Envelope>';

        $this->expectException(SalesforceException::class);
        $this->outboundMessageObjectNameResolver->resolve($xml);
    }

    /**
     * @covers ::resolve()
     */
    public function testRetrieveThrowsExceptionOnEmptyXml()
    {
        $xml = '';

        $this->expectException(SalesforceException::class);
        $this->outboundMessageObjectNameResolver->resolve($xml);
    }
}
