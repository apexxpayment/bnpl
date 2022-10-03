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
 * Class VoidSale
 * @package Apexx\Bnpl\Gateway\Http\Client
 */
class VoidSale implements ClientInterface
{
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
     * VoidSale constructor.
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param BnplHelper $bnplHelper
     * @param CustomLogger $customLogger
     */
    public function __construct(
        ApexxBaseHelper $apexxBaseHelper,
        BnplHelper $bnplHelper,
        CustomLogger $customLogger
    ) {
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->bnplHelper = $bnplHelper;
        $this->customLogger = $customLogger;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array|mixed
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();
        // Set capture url
        $url = $this->apexxBaseHelper->getApiEndpoint().'cancel/payment/'.$request['transactionId'];
        //Set parameters for curl
        unset($request['transactionId']);
        $resultCode = json_encode($request);

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);
        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('Bnpl Void Request:', $request);
        $this->customLogger->debug('Bnpl Void Response:', $responseResult);

        return $responseResult;
    }
}
