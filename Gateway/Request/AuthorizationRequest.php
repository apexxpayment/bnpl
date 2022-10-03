<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Apexx\Bnpl\Helper\Data as BnplHelper;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session as CheckoutSession;

class AuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var BnplHelper
     */
    protected  $bnplHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @param ConfigInterface $config
     * @param BnplHelper $bnplHelper
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        ConfigInterface $config,
        BnplHelper $bnplHelper,
        ApexxBaseHelper $apexxBaseHelper,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        CheckoutSession $checkoutSession
    ) {
        $this->config = $config;
        $this->bnplHelper = $bnplHelper;
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->cartRepository = $cartRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */

    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $shippingNetPrice = $this->checkoutSession->getQuote()->getShippingAddress()->getShippingAmount();
        $shippingTaxAmount = $this->checkoutSession->getQuote()->getShippingAddress()->getShippingTaxAmount();
        $shippingGrossPrice = $this->checkoutSession->getQuote()->getShippingAddress()->getShippingInclTax();

        /** @var PaymentDataObjectInterface $payment */
        $payment = $buildSubject['payment'];
        $order= $payment->getOrder();

        $delivery = $order->getShippingAddress();
        $billing = $order->getBillingAddress();
        $billingAddress = '';
        $shippingAddress = '';

        // Billing Address
        if ($billing->getStreetLine1()){
            $billingAddress .= $billing->getStreetLine1().$billing->getStreetLine2();
        } elseif (isset($billing->getStreet()[0])) {
            $billingAddress .= $billing->getStreet()[0];
            if (isset($billing->getStreet()[1])) {
                $billingAddress .= $billing->getStreet()[1];
            }
        }

        // Shipping Address
        if ($delivery->getStreetLine1()){
            $shippingAddress .= $delivery->getStreetLine1().$delivery->getStreetLine2();
        } elseif (isset($billing->getStreet()[0])) {
            $shippingAddress .= $delivery->getStreet()[0];
            if (isset($billing->getStreet()[1])) {
                $shippingAddress .= $delivery->getStreet()[1];
            }
        }

        $total = $buildSubject['amount'];
        $selectedPaymentMethod = $this->checkoutSession->getApexxBnplPaymentCode();
        $finalPaymentAction = false;

        if ($this->bnplHelper->getBnplPaymentAction($selectedPaymentMethod) == 'authorize') {
            $finalPaymentAction =  'false';
        } else {
            $finalPaymentAction = 'true';
        }

        $subTotal = 0;
        $discountAmount = 0;
        $taxAmount = 0;
        $finalAmount =0;
        foreach ($order->getItems() as $item) {
            if ($item->getProductType() != 'bundle') {
                $subTotal = $subTotal + ( $item->getPrice() * $item->getQtyOrdered());
                $discountAmount =  $discountAmount + $item->getDiscountAmount();
                $taxAmount =  $taxAmount + $item->getTaxAmount();
                $finalAmount = $finalAmount + ($item->getPrice() * $item->getQtyOrdered());
            }
        }

        $formFields=[];
        $bnplAmount = (($subTotal + $taxAmount) * 100);
        if ($discountAmount > 0) {
            $bnplNetAmount = (($finalAmount - $discountAmount)  * 100);
        } else {
            $bnplNetAmount = $subTotal * 100;
        }

        $requestData= [
            "organisation" => $this->apexxBaseHelper->getOrganizationId(),
            "currency" => $order->getCurrencyCode(),
            //"amount" => (($subTotal + $taxAmount + $shippingNetPrice) - $discountAmount) * 100,
            "amount" => $order->getGrandTotalAmount() * 100,
            "net_amount" => $bnplNetAmount + ($shippingNetPrice * 100),
            "capture_now" => $finalPaymentAction,
            "dynamic_descriptor" => 'aa',
            "merchant_reference" => 'BNPLPAYMENT'.$order->getOrderIncrementId(),
            "locale" => str_replace("_","-",$this->apexxBaseHelper->getStoreLocale()),
            "customer_ip" => "",
            "user_agent" => $this->apexxBaseHelper->getUserAgent(),
            "webhook_transaction_update" => "",
            "shopper_interaction" => "ecommerce",
            "bnpl" => [
                "payment_method" => $selectedPaymentMethod,
                "payment_type" => "installment"
            ],
            "redirect_urls" => [
                "success" => $this->apexxBaseHelper->getStoreUrl().'apexxbnpl/index/response',
                "failed" => $this->apexxBaseHelper->getStoreUrl().'apexxbnpl/index/response',
                "cancelled" => $this->apexxBaseHelper->getStoreUrl().'apexxbnpl/index/response'
            ]
        ];

        foreach ($order->getItems() as $item) {
            if ($item->getProductType() != 'bundle') {
                $itemwisePrice = $item->getPrice() * 100;
                $formFields['items'][] = [
                    'product_id' => $item->getProductId(),
                    'group_id' => "string",
                    'item_description' => $item->getName(),
                    'net_unit_price' => $itemwisePrice,
                    'gross_unit_price' => ($item->getPriceInclTax() * 100),
                    'quantity' => $item->getQtyOrdered(),
                    'vat_percent' => $item->getTaxPercent(),
                    'vat_amount' => (($item->getPriceInclTax() - $item->getPrice()) * 100),
                    "discount" => 0
                ];
            }
        }

        if ($discountAmount > 0) {
            $formFields['items'][] =
                [
                    'item_type'=> 'discount',
                    'additional_information'=> '',
                    "discount" => 0,
                    'gross_unit_price' => ($discountAmount * 100),
                    'group_id'=> 'discount',
                    'item_description'=> 'Discount',
                    'net_unit_price' =>  ($discountAmount * 100),
                    'product_id'=> 'string',
                    'quantity'=> 1,
                    'vat_amount' => 0,
                    'vat_percent'=> 0
                ];
        }

        if ($shippingNetPrice > 0) {
            $formFields['items'][] =
                [
                    'item_type'=> 'shipping',
                    'additional_information'=> '',
                    "discount" => 0,
                    'gross_unit_price' => ($shippingGrossPrice * 100),
                    'group_id'=> 'shipping',
                    'item_description'=> 'shipping',
                    'net_unit_price' =>  ($shippingNetPrice * 100),
                    'product_id'=> 'string',
                    'quantity'=> 1,
                    'vat_amount' => ($shippingTaxAmount * 100),
                    'vat_percent'=> 1
                ];
        }

        $requestData2 = [
            "customer" => [
                "email" => $billing->getEmail(),
                "phone" => $billing->getTelephone()
            ],
            "billing_address" => [
                "first_name" => $billing->getFirstname(),
                "last_name" => $billing->getLastname(),
                "email" => $billing->getEmail(),
                "address" => $billingAddress,
                "city" => $billing->getCity(),
                "state" => $billing->getRegionCode(),
                "postal_code" => $billing->getPostcode(),
                "country" => $billing->getCountryId(),
                "phone" => $billing->getTelephone()
            ],
            "delivery_address" => [
                "first_name" => $delivery->getFirstname(),
                "last_name" => $delivery->getLastname(),
                "phone" => $delivery->getTelephone(),
                "salutation" => "Mr",
                "type" => "company",
                "care_of" => "string",
                "address" => $shippingAddress,
                "address2" => $shippingAddress,
                "city" =>  $delivery->getCity(),
                "state" => $delivery->getRegionCode(),
                "postal_code" => $delivery->getPostcode(),
                "country" => $delivery->getCountryId(),
                "method" => "delivery"
            ]
        ];
        $requestData3 = ["duplicate_check" => "false"];
        $requestData = array_merge($requestData,$formFields,$requestData2, $requestData3);

        return $requestData;
    }
}
