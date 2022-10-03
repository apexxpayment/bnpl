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
 * Class CaptureSale
 * @package Apexx\Bnpl\Gateway\Http\Client
 */
class CaptureSale implements ClientInterface
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
     * @var BnplHelper
     */
    protected  $bnplHelper;

    /**
     * @var CustomLogger
     */
    protected $customLogger;

    /**
     * CaptureSale constructor.
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
        if (isset($request['transaction_id'])){
            $url = $this->apexxBaseHelper->getApiEndpoint().'capture/'.$request['transaction_id'];
        } else {
            $url = $this->apexxBaseHelper->getApiEndpoint().'payment/bnpl';
        }

        //Set parameters for curl
        $resultCode = json_encode($request);

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);

        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);
        $this->customLogger->debug('Bnpl Capture Request:', $request);
        $this->customLogger->debug('Bnpl Capture Response:', $responseResult);

        return $responseResult;
    }
}
