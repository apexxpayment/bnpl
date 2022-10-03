<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

class ResponseCaptureValidator extends AbstractValidator
{
    /**
     * Performs validation of result code
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) {
            throw new \InvalidArgumentException('Response does not exist');
        }

        $response = $validationSubject['response'];

        $paymentDataObjectInterface = \Magento\Payment\Gateway\Helper\SubjectReader::readPayment($validationSubject);
        $payment = $paymentDataObjectInterface->getPayment();

        if (isset($response['url'])) {
            $payment->setAdditionalInformation('_id', $response['_id']);
            $payment->setAdditionalInformation('url', $response['url']);
            return $this->createResult(
                true,
                []
            );
        } elseif (isset($response['status'])) {
            if ($response['status'] == 'CAPTURED') {
                if (isset($response['_id'])) {
                    $payment->setAdditionalInformation('_id', $response['_id']);
                }
                if (isset($response['payment_method'])) {
                    $payment->setAdditionalInformation('payment_method', $response['payment_method']);
                }
                return $this->createResult(
                    true,
                    []
                );
            }  elseif ($response['status'] == 'FAILED') {
                if (isset($response['errors'])) {
                    if (isset($response['errors'][0]['error_message'])) {
                        return $this->createResult(
                            false,
                            [__($response['errors'][0]['error_message'])]
                        );
                    } else {
                        return $this->createResult(
                            false,
                            [__($response['reason_message'])]
                        );
                    }
                }
            } elseif ($response['status'] == 'DECLINED') {
                if (isset($response['errors'])) {
                    if (isset($response['errors'][0]['error_message'])) {
                        return $this->createResult(
                            false,
                            [__($response['errors'][0]['error_message'])]
                        );
                    } else {
                        return $this->createResult(
                            false,
                            [__($response['reason_message'])]
                        );
                    }
                } elseif (isset($response['reason_message'])) {
                    return $this->createResult(
                        false,
                        [__($response['reason_message'])]
                    );
                } else {
                    return $this->createResult(
                        false,
                        [__('Gateway rejected the transaction.')]
                    );
                }
            } else {
                return $this->createResult(
                    false,
                    [__('Gateway rejected the transaction.')]
                );
            }
        }  else {
            return $this->createResult(
                false,
                [__('Gateway rejected the transaction.')]
            );
        }
    }
}
