<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\Encryption\EncryptorInterface ;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\Serialize\Serializer\Json as SerializeJson;
use \Magento\Framework\HTTP\Adapter\CurlFactory;
use \Magento\Framework\HTTP\Header as HttpHeader;
use \Magento\Sales\Model\OrderRepository;
use \Magento\Sales\Api\Data\TransactionInterface;
use \Magento\Sales\Api\TransactionRepositoryInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\FilterBuilder;
use \Psr\Log\LoggerInterface;
use Apexx\Bnpl\Model\ResourceModel\PayGroup\CollectionFactory;

/**
 * Class Data
 * @package Apexx\Bnpl\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Config paths
     */
    const XML_PATH_CONFIG_PAYMENT_BNPL = 'payment/bnpl_gateway';
    const XML_PATH_PAYMENT_BNPL        = 'payment/apexx_section/apexxpayment/bnpl_gateway';
    const XML_PATH_CAPTURE_MODE        = '/capture_mode';
    const XML_PATH_PAYMENT_LIST       = '/payment_method_list';
    const XML_PATH_PAYMENT_TYPE        = '/payment_type';
    const XML_PATH_PAYMENT_ACTION      = '/payment_action';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var SerializeJson
     */
    protected $serializeJson;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var HttpHeader
     */
    protected $httpHeader;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchBuilder;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Data constructor
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param JsonFactory $resultJsonFactory
     * @param SerializeJson $serializeJson
     * @param CurlFactory $curlFactory
     * @param HttpHeader $httpHeader
     * @param OrderRepository $orderRepository
     * @param TransactionRepositoryInterface $transactionRepository
     * @param SearchCriteriaBuilder $searchBuilder
     * @param FilterBuilder $filterBuilder
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        JsonFactory $resultJsonFactory,
        SerializeJson $serializeJson,
        curlFactory $curlFactory,
        HttpHeader $httpHeader,
        OrderRepository $orderRepository,
        TransactionRepositoryInterface $transactionRepository,
        SearchCriteriaBuilder $searchBuilder,
        FilterBuilder $filterBuilder,
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor ;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->serializeJson = $serializeJson;
        $this->curlFactory = $curlFactory;
        $this->httpHeader = $httpHeader;
        $this->orderRepository  = $orderRepository;
        $this->transactionRepository = $transactionRepository;
        $this->searchBuilder = $searchBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Get config value at the specified key
     *
     * @param string $key
     * @return mixed
     */
    public function getConfigPathValue($key)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CONFIG_PAYMENT_BNPL . $key,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * Get config value at the specified key
     *
     * @param string $key
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_BNPL . $key,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getCaptureMode()
    {
        return $this->getConfigPathValue(self::XML_PATH_CAPTURE_MODE);
    }

    /**
     * @return string
     */
    public function getCustomPaymentType()
    {
        return $this->getConfigPathValue(self::XML_PATH_PAYMENT_TYPE);
    }

    /**
     * @param $orderId
     * @return float|null
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getOrderTotalInvoiced($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        return $order->getTotalInvoiced();
    }

    /**
     * @return mixed
     */
    public function getActiveBnplMethod()
    {
        return $this->getConfigValue(self::XML_PATH_PAYMENT_LIST);
    }

    /**
     * @param $code
     * @return mixed|string
     */
    public function getBnplPaymentAction($code)
    {
        $bnplAction = '';
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('payment_code', ['eq' => $code]);
        $paymentInfo = $collection->getData();
        if (count($paymentInfo) > 0 && isset($paymentInfo[0]['payment_action'])){
            $bnplAction = $paymentInfo[0]['payment_action'];
        }

        return $bnplAction;
    }
}
