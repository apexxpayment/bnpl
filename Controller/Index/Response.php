<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Controller\Index;

use Exception;
use \Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order\Payment\Transaction\Builder;
use Apexx\Bnpl\Helper\InvoiceGenerate as CustomInvoice;
use Magento\Framework\Session\SessionManagerInterface;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;


/**
 * Class Response
 * @package Apexx\Bnpl\Controller\Index
 */
class Response extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Builder
     */
    private $transactionBuilder;
    /**
     * @var CustomInvoice
     */
    protected $customInvoice;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var CustomLogger
     */
    protected $customLogger;

    /**
     * Response constructor
     *
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Http $request
     * @param ManagerInterface $messageManager
     * @param UrlInterface $urlInterface
     * @param Session $checkoutSession
     * @param OrderRepository $orderRepository
     * @param Order $order
     * @param OrderFactory $orderFactory
     * @param LoggerInterface $logger
     * @param Builder $transactionBuilder
     * @param CustomInvoice $customInvoice
     * @param SessionManagerInterface $sessionManager
     * @param CustomLogger $customLogger
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        RedirectFactory $resultRedirectFactory,
        Http $request,
        ManagerInterface $messageManager,
        UrlInterface $urlInterface,
        Session $checkoutSession,
        OrderRepository $orderRepository,
        Order $order,
        OrderFactory $orderFactory,
        LoggerInterface $logger,
        Builder $transactionBuilder,
        CustomInvoice $customInvoice,
        SessionManagerInterface $sessionManager,
        CustomLogger $customLogger
    )
    {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->urlInterface = $urlInterface;
        $this->orderRepository = $orderRepository;
        $this->order           = $order;
        $this->orderFactory = $orderFactory;
        $this->logger = $logger;
        $this->transactionBuilder = $transactionBuilder;
        $this->customInvoice = $customInvoice;
        $this->sessionManager = $sessionManager;
        $this->customLogger = $customLogger;
    }

    public function execute()
    {
        try {
            $response = $this->request->getParams();
            $this->customLogger->debug('Bnpl Success Response:', $response);
            $resultRedirect = $this->resultRedirectFactory->create();
            $transactionId = $response['_id'];
            $status = $response['status'];
            $paymentMethod=$response['payment_method'];

            if (isset($response['merchant_reference'])) {
                $customerOrder = explode('BNPLPAYMENT', $response['merchant_reference']);
                if (isset($customerOrder[1])) {
                    $order = $this->order->loadByIncrementId($customerOrder[1]);
                }
            }

            $total = $order->getTotalPaid();

            /** @var \Magento\Sales\Model\Order\Payment $payment */
            $payment = $order->getPayment();

            if ($status == 'AUTHORISED') {
                $payment->setLastTransId($transactionId);
                $payment->setParentTransactionId(null);

                $transaction = $this->transactionBuilder->setPayment($payment)
                    ->setOrder($order)
                    ->setTransactionId($transactionId)
                    ->addAdditionalInformation('raw_details_info', $response)
                    ->setFailSafe(true)
                    ->build('authorization');
                $transaction->setIsClosed(false);

                $payment->addTransactionCommentsToOrder($transaction, __('Authorized amount of %1.', $order->getBaseCurrency()->formatTxt($total)));

               // $order->setStatus('processing');
                $order->setStatus('authorised');
                $order->setState('processing');

                $payment->save();
                $order->save();
                $transaction->save();

            } elseif ($status == 'CAPTURED') {
                $payment->setLastTransId($transactionId);
                $payment->setTransactionId($transactionId);
                $payment->setIsTransactionClosed(true);

                if (isset($response['_id'])) {
                    $payment->setAdditionalInformation('_id', $response['_id']);
                }
                if (isset($response['merchant_reference'])) {
                    $payment->setAdditionalInformation('merchant_reference', $response['merchant_reference']);
                }
                if (isset($response['payment_method'])) {
                    $payment->setAdditionalInformation('payment_method', $response['payment_method']);
                }
                if (isset($response['status'])) {
                    $payment->setAdditionalInformation('status', $response['status']);
                }

                $transaction = $this->transactionBuilder->setPayment($payment)
                    ->setOrder($order)
                    ->setTransactionId($transactionId)
                    ->addAdditionalInformation('raw_details_info', $response)
                    ->setFailSafe(true)
                    ->build('capture');
                $transaction->setIsClosed(true);

                $payment->addTransactionCommentsToOrder($transaction, __('Captured amount of %1 online', $order->getBaseCurrency()->formatTxt($total)));
                $payment->save();
                $order->save();
                $transaction->save();
                $this->customInvoice->createInvoice($order->getId(), $total,$transactionId);
            } elseif ($status == 'PENDING') {
                $payment->setLastTransId($transactionId);
                $payment->setParentTransactionId(null);
                if ($response['payment_method']) {
                    $payment->setAdditionalInformation('payment_method', $response['payment_method']);
                }
                $transaction = $this->transactionBuilder->setPayment($payment)
                    ->setOrder($order)
                    ->setTransactionId($transactionId)
                    ->addAdditionalInformation('raw_details_info', $response)
                    ->setFailSafe(true)
                    ->build('authorization');
                $transaction->setIsClosed(false);

                $payment->addTransactionCommentsToOrder($transaction, __('Authorized amount of %1.', $order->getBaseCurrency()->formatTxt($total)));
                //$order->setStatus('processing');
               // $order->setState('processing');
                $payment->save();
                $order->save();
                $transaction->save();
            } else {
                $orderStatus = strtolower($response['status']);
                $order->setStatus($orderStatus);
                $order->save();
                $order->getIncrementId();
                $this->setBnplFailureMessage($response,$order->getIncrementId());

                return $resultRedirect->setPath('apexxbnpl/payment/failure');
            }

            return $resultRedirect->setPath('checkout/onepage/success');
        } catch (\Exception $e){
            $this->logger->critical($e);
        }
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

    /**
     * @param $response
     * @param $orderid
     */
    public function setBnplFailureMessage($response,$orderid)
    {
        $this->sessionManager->start();
        $this->sessionManager->setData('bnplfailure',$response);
        $this->sessionManager->setData('orderId',$orderid);
    }

    public function getOrderIdByIncrementId($incrementId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $incrementId)->create();

        try {
            $order = $this->orderRepository->getList($searchCriteria);

        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }

        return $order;
    }
}
