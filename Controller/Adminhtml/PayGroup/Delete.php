<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Controller\Adminhtml\PayGroup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Apexx\Bnpl\Api\PayGroupRepositoryInterface;

class Delete extends Action
{
    /**
     * @var PayGroupRepositoryInterface
     */
    protected $paygroupRepository;

    /**
     * @param PayGroupRepositoryInterface $paygroupRepository
     * @param Context $context
     */
    public function __construct(
        PayGroupRepositoryInterface $paygroupRepository,
        Context $context
    ) {
        $this->paygroupRepository = $paygroupRepository;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('Unable to proceed. Please, try again.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', array('_current' => true));
        }
        try{
            $this->paygroupRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('Record Has been deleted !'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error while trying to delete record: '));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index', array('_current' => true));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', array('_current' => true));
    }
}
