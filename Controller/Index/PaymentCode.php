<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Controller\Index;

use Magento\Checkout\Model\Session as CheckoutSession;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;

class PaymentCode extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CustomLogger
     */
    protected $customLogger;

    public function __construct(
        Context $context,
        RequestInterface $request,
        CheckoutSession $checkoutSession,
        CustomLogger $customLogger
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->customLogger = $customLogger;
    }

    public function execute()
    {
        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $responseContent = [
            'success' => true,
            'error_message' => '',
        ];

        $this->checkoutSession->unsApexxBnplPaymentCode();
        $paymentType = $this->request->getParam('paymentMethodType');
        $this->checkoutSession->setApexxBnplPaymentCode($paymentType);

        return $controllerResult->setData($responseContent);
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ? bool
    {
        return true;
    }
}
