<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Plugin\Method;

use Magento\Payment\Model\Method\Adapter;
use Magento\Checkout\Model\Session;
use Apexx\Bnpl\Helper\Data as ConfigHelper;

/**
 * Class ApexxAdapter
 * @package Apexx\Bnpl\Model\Method
 */
class ApexxAdapter
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var ConfigHelper
     */
    protected  $configHelper;

    /**
     * ApexxAdapter constructor
     *
     * @param Session $checkoutSession
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        Session $checkoutSession,
        ConfigHelper $configHelper
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->configHelper = $configHelper;
    }

    /**
     * @param Adapter $subject
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetConfigPaymentAction(Adapter $subject, $result)
    {
        $paymentMethod = $this->checkoutSession->getQuote()->getPayment()->getMethodInstance()->getCode();
        $selectedPaymentMethod = $this->checkoutSession->getApexxBnplPaymentCode();
        $paymentmode = $this->configHelper->getBnplPaymentAction($selectedPaymentMethod);

        if ($paymentMethod == 'bnpl_gateway') {
            $result = $paymentmode;
        }

        return $result;
    }
}
