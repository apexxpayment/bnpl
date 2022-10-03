<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session As CheckoutSession;
use Apexx\Base\Helper\Data as ApexxBaseHelper;

/**
 * Class DisabledBnplPayment
 * @package Apexx\Bnpl\Observer
 */
class DisabledBnplPayment implements ObserverInterface
{
    /**
     * @var Session
     */
	protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var ApexxBaseHelper
     */
    protected $apexxBaseHelper;

    /**
     * DisabledBnplPayment constructor.
     *
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CartRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param ApexxBaseHelper $apexxBaseHelper
     */
	public function __construct(
	    Session $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        ApexxBaseHelper $apexxBaseHelper
    ) {
		$this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->apexxBaseHelper = $apexxBaseHelper;
	}

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $paymentMethod = $observer->getEvent()->getMethodInstance()->getCode();
        $result = $observer->getEvent()->getResult();

        $apiType = $this->apexxBaseHelper->getApiType();

        if ($apiType == 'Atomic') {
            if ($paymentMethod == 'bnpl_gateway') {
                $result->setData('is_available', true);
                return;
            }
        } else {
            if ($paymentMethod == 'bnpl_gateway') {
                $result->setData('is_available', false);
                return;
            }
        }
    }
}
