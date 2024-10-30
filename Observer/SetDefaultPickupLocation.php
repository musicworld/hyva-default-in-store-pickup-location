<?php
namespace Musicworld\DefaultPickupLocation\Observer;

use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\InventoryInStorePickupApi\Model\SearchRequestBuilderInterface;
use Magento\Quote\Model\Quote;
use Magento\InventoryInStorePickupApi\Api\GetPickupLocationsInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;


class SetDefaultPickupLocation implements ObserverInterface
{
    protected ScopeConfigInterface $scopeConfig;
    protected LoggerInterface $logger;
    protected GetPickupLocationsInterface $getPickupLocations;
    protected SearchRequestBuilderInterface $searchRequestBuilder;

    public function __construct(
        GetPickupLocationsInterface $getPickupLocations,
        SearchRequestBuilderInterface $searchRequestBuilder,
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->getPickupLocations = $getPickupLocations;
        $this->searchRequestBuilder = $searchRequestBuilder;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer): void
    {

        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        $shippingMethod = $shippingAddress->getShippingMethod();


        // Überprüfen, ob die Versandmethode In-Store Pickup ist
        if (str_contains($shippingMethod, 'instore_pickup')) {


            $pickupLocationCode = $this->scopeConfig->getValue('default_pickup_location/configuration/pickup_location_code', ScopeInterface::SCOPE_STORE);

            $searchRequest = $this->searchRequestBuilder->setPickupLocationCodeFilter($pickupLocationCode)
                ->setScopeCode('base')
                ->setScopeType('website')
                ->setPageSize(1)
                ->create();

            $pickupLocation = $this->getPickupLocations->execute($searchRequest)->getItems()[0];
            if ($pickupLocation) {

                $shippingAddress->setCity($pickupLocation->getCity());
                $shippingAddress->setPostcode($pickupLocation->getPostcode());
                $shippingAddress->setStreet([$pickupLocation->getStreet()]);
                $shippingAddress->setCountryId($pickupLocation->getCountryId());
                $shippingAddress->setRegionId($pickupLocation->getRegionId());
                $shippingAddress->setTelephone($pickupLocation->getPhone());

                $shippingAddress->setFax($pickupLocation->getFax());
                $shippingAddress->setCompany($pickupLocation->getName()); // Annahme: Firmenname ist gleich Pickup-Location-Name
                $shippingAddress->setRegion($pickupLocation->getRegion());


                $shippingAddress->setEmail($pickupLocation->getEmail());
                $shippingAddress->setFirstname($pickupLocation->getContactName()); // oder einen anderen relevanten Namen
                $shippingAddress->setLastname('- Abholung'); // Falls erforderlich
                $shippingAddress->setSameAsBilling(false);

                $extensionAttributes = $shippingAddress->getExtensionAttributes();


                $extensionAttributes->setPickupLocationCode($pickupLocationCode);
                $shippingAddress->setExtensionAttributes($extensionAttributes);

            }
        }
    }
}
