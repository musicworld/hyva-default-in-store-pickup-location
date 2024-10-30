<?php
namespace Musicworld\DefaultInStorePickupLocation\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class PickupLocations implements OptionSourceInterface
{
    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * Constructor
     *
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->sourceRepository = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get all pickup locations
     * @return array
     */
    public function toOptionArray(): array
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $sources = $this->sourceRepository->getList($searchCriteria)->getItems();
        $options = [];


        foreach ($sources as $source) {
            // Only add sources that are enabled for pickup
            if ($source->isEnabled() && $source->getExtensionAttributes()) {
                $options[] = [
                    'value' => $source->getSourceCode(),
                    'label' => $source->getName().' [ '.$source->getSourceCode().']'
                ];
            }
        }

        return $options;
    }
}
