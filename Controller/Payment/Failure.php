<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Controller\Payment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Failure
 * @package Apexx\Bnpl\Controller\Payment
 */
class Failure extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
	protected $resultPageFactory;

    /**
     * Failure constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
	public function __construct(
	    Context $context,
        PageFactory $resultPageFactory
    ) {
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
	public function execute() {
		return $this->resultPageFactory->create();
	}
}
