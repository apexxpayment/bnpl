<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

class PaymentAction implements OptionSourceInterface
{
    /**
     * Different payment actions.
     */
    const ACTION_AUTHORIZE = 'authorize';
    const ACTION_AUTHORIZE_CAPTURE = 'authorize_capture';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
                    ['value' => self::ACTION_AUTHORIZE,
                        'label' => __('Authorize Only')],
                    ['value' => self::ACTION_AUTHORIZE_CAPTURE,
                        'label' => __('Authorize and Capture')]
        ];
    }
}
