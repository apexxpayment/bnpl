<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Api\Data;


interface PayGroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get paygroup list
     *
     * @return \Apexx\Bnpl\Api\Data\PayGroupSearchResultsInterface[]
     */
    public function getItems();

    /**
     * Set paygroup list
     *
     * @param \Apexx\Bnpl\Api\Data\PayGroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
