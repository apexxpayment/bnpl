<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

class PaymentType implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
                    ['value' => '', 'label' => __('Select Type')],
                    ['value' => 'installment', 'label' => __('Installment')],
                    ['value' => 'invoice', 'label' => __('Invoice')],
                    ['value' => 'Direct Debit', 'label' => __('Direct Debit')]
        ];
    }
}
