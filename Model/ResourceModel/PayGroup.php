<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


class PayGroup extends AbstractDb
{
    /**
     * Resource initialisation
     */
    protected function _construct()
    {
        $this->_init('apexx_bnpl_paygroup', 'id');
    }
}
