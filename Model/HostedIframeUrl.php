<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Model;

use \Magento\Sales\Api\OrderRepositoryInterface;
use Apexx\Bnpl\Model\Ui\ConfigProvider;
use Apexx\Bnpl\Helper\Data as BnplHelper;
use Psr\Log\LoggerInterface;

/**
 * Class HostedIframeUrl
 * @package Apexx\Bnpl\Model
 */
class HostedIframeUrl implements \Apexx\Bnpl\Api\HostedIframeUrlInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var BnplHelper
     */
    protected  $bnpltHelper;

    /**
     * Logger for exception details
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * HostedIframeUrl constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param BnplHelper $bnpltHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        BnplHelper $bnpltHelper,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->bnplHelper = $bnpltHelper;
        $this->logger = $logger;
    }

    /**
     * @param string $orderId
     * @return array|false|string
     */
    public function getHostedIframeUrl($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $payment = $order->getPayment();
        $response = [];

        try {
            if ($payment->getMethod() == 'bnpl_gateway') {
                $additionalInformation = $payment->getAdditionalInformation();
                $iframeUrl = $additionalInformation['url'];
                $response['url'] = $iframeUrl;
            }
            return json_encode($response);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $response;
    }
}
