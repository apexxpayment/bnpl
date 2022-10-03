<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Block\Adminhtml\PayGroup\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;


class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Prepare Back Button HTML
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href= '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
