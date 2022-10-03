<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Api;


interface HostedIframeUrlInterface
{
    /**
     * @param string $orderId
     * @return string
     */
    public function getHostedIframeUrl($orderId);
}
