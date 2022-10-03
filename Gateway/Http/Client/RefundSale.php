<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */
namespace Apexx\Bnpl\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\HTTP\Client\Curl;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Bnpl\Helper\Data as BnplHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class RefundSale
 * @package Apexx\Bnpl\Gateway\Http\Client
 */
class RefundSale implements ClientInterface
{
    const SUCCESS = 1;
    const FAILURE = 0;

    /**
     * @var array
     */
    private $results = [
        self::SUCCESS,
        self::FAILURE
    ];

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * @var BnplHelper
     */
    protected  $bnplHelper;

    /**
     * @var CustomLogger
     */
    protected $customLogger;

    /**
     * RefundSale constructor.
     * @param Curl $curl
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param BnplHelper $bnplHelper
     * @param CustomLogger $customLogger
     */
    public function __construct(
        Curl $curl,
        ApexxBaseHelper $apexxBaseHelper,
        BnplHelper $bnplHelper,
        CustomLogger $customLogger
    ) {
        $this->curlClient = $curl;
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->bnplHelper = $bnplHelper;
        $this->customLogger = $customLogger;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();
        // Set capture url
        $url = $this->apexxBaseHelper->getApiEndpoint().'refund/'.$request['transactionId'];
        //unset($request['transactionId']);
        //Set parameters for curl
        $resultCode = json_encode($request);
        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);
        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('Bnpl Refund Request:', $request);
        $this->customLogger->debug('Bnpl Refund Response:', $responseResult);

        return $responseResult;
    }
}
