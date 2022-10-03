<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Controller\Adminhtml\PayGroup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Apexx\Bnpl\Model\PayGroup;
use Apexx\Bnpl\Model\PayGroupFactory as PayGroupFactory;
use Apexx\Bnpl\Model\ResourceModel\PayGroup as ResouceModel;


class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PayGroupFactory
     */
    protected $paygroupFactory;

    /**
     * @var ResouceModel
     */
    protected $resouce;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PayGroupFactory $paygroupFactory
     * @param ResouceModel $resouce
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PayGroupFactory $paygroupFactory,
        ResouceModel $resouce
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->paygroupFactory = $paygroupFactory;
        $this->resouce = $resouce;
        parent::__construct($context);
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Apexx_Adminmenu::PayGroups');
    }

    /**
     * Edit Project
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var PayGroup $model */
        $model = $this->paygroupFactory->create();

        if ($id) {
            $this->resouce->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Record no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Apexx_Adminmenu::PayGroups');
        $resultPage->getConfig()->getTitle()->prepend(__('Payment'));

        return $resultPage;
    }
}
