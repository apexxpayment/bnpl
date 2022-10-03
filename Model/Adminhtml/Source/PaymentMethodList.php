<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Model\Adminhtml\Source;

use Apexx\Bnpl\Model\ResourceModel\PayGroup\CollectionFactory;

/**
 * Class PaymentMethodList
 * @package Apexx\Bnpl\Model\Adminhtml\Source
 */
class PaymentMethodList
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * PaymentMethodList constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $paymentList = [];
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', ['eq' => 1]);
        if (count($collection) > 0) {
            foreach ($collection->getData() as $eachItem) {
                $paymentList[] = ['value' => $eachItem['payment_code'], 'label' => $eachItem['payment_title']];
            }
        }

        return $paymentList;
    }
}
