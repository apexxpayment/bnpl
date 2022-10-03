<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Block\Adminhtml\PayGroup\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


class ResetButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Prepare Reset Button HTML
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'on_click' => 'location.reload();',
            'class' => 'reset',
            'sort_order' => 30
        ];
    }
}
