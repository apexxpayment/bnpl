<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Apexx\Bnpl\Helper\Data as ConfigHelper;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class OrderObserver
 * @package Apexx\Bnpl\Observer
 */

class OrderObserver extends AbstractDataAssignObserver
{
    /**
     * @var ConfigHelper
     */
    protected  $configHelper;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @param ConfigHelper $configHelper
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        ConfigHelper $configHelper,
        CheckoutSession $checkoutSession
    ) {
        $this->configHelper = $configHelper;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $eventName = $observer->getEvent()->getName();
        $method = $order->getPayment()->getMethod();
        $selectedPaymentMethod = $this->checkoutSession->getApexxBnplPaymentCode();
        $paymentmode = $this->configHelper->getBnplPaymentAction($selectedPaymentMethod);
        if ($method == 'bnpl_gateway' && $paymentmode == 'authorize') {
            switch ($eventName) {
                case 'sales_order_place_after':
                    $this->updateOrderState($observer);

                    break;
            }
        }
    }

    /**
     * @param $observer
     */
    public function updateOrderState($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $order->setState('pending');
        $order->setStatus('pending');
        $order->setIsNotified(false);
        //$order->addStatusToHistory($status = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT, $comment, $isCustomerNotified = false);
        $order->save();
    }
}
