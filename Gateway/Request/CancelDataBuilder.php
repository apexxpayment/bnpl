<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class CancelDataBuilder
 * @package Apexx\Bnpl\Gateway\Request
 */
class CancelDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Create capture request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();

        // Send Parameters to Bnpl Payment Client
        $order = $paymentDO->getOrder();
        $total = $order->getGrandTotalAmount();
        $paymentInfo = $orderPayment->getAdditionalInformation();
        $paymentMethod = $paymentInfo['payment_method'];

        $amountAuthorized = number_format($orderPayment['amount_authorized'], 2, '.', '');

        $requestData = [
            "transactionId" => $orderPayment->getLastTransId(),
            "amount" => ($amountAuthorized * 100),
        ];

        return $requestData;
    }
}
