<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Model\ResourceModel\PayGroup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            'Apexx\Bnpl\Model\PayGroup',
            'Apexx\Bnpl\Model\ResourceModel\PayGroup'
        );
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('id', 'payment_title');
    }
}
