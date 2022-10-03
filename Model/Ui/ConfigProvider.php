<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Apexx\Bnpl\Gateway\Http\Client\ClientMock;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Apexx\Bnpl\Model\ResourceModel\PayGroup\CollectionFactory;
use Apexx\Bnpl\Helper\Data as ConfigHelper;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'bnpl_gateway';

    protected $storeManager;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var ConfigHelper
     */
    protected  $configHelper;

    /**
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param Escaper $escaper
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        Escaper $escaper,
        ConfigHelper $configHelper
    ) {
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->escaper = $escaper;
        $this->configHelper = $configHelper;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $config['payment']['logo'][self::CODE] = $this->getLogo(self::CODE);

        return $config;
    }

    /**
     * @param $code
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getLogo($code)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('status', ['eq' => 1]);
        $activePayment = $this->configHelper->getActiveBnplMethod();
        $paymentLogoList = [];
        if (count($collection) > 0 && $activePayment) {
            $eachPayment = explode(",",$activePayment);
            foreach ($collection->getData() as $eachItem) {
                if (in_array($eachItem['payment_code'],$eachPayment)) {
                    $path = $this->storeManager->getStore()->getBaseUrl(
                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        ) . 'bnpl/tmp/group/';
                    $logoLink = nl2br($this->escaper->escapeHtml($path.$eachItem['payment_image']));
                    $paymentLogoList[] = ['link' => $logoLink, 'paymentcode' => $eachItem['payment_code'], 'title' => $eachItem['payment_title']];
                }
            }
        }

        return $paymentLogoList;
    }
}
