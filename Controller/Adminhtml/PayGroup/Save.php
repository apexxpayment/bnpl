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
use Apexx\Bnpl\Model\PayGroupFactory;
use Apexx\Bnpl\Api\PayGroupRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class Save extends Action
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
     * @var PayGroupRepositoryInterface
     */
    protected $paygroupRepository;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Group constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PayGroupFactory $paygroupFactory
     * @param PayGroupRepositoryInterface $paygroupRepository
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PayGroupFactory $paygroupFactory,
        PayGroupRepositoryInterface $paygroupRepository,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->paygroupFactory = $paygroupFactory;
        $this->paygroupRepository = $paygroupRepository;
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
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
     * Save Action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();

        if ($data) {
            if (empty($data['id'])) {
                $data['id'] = null;
            }

            /** @var PayGroup $model */
            $model = $this->paygroupFactory->create();
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $model = $this->paygroupRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This block no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $data = $this->filterImageData($data);
            $model->setData($data);
            try {
                $this->paygroupRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the payment.'));
                $this->dataPersistor->clear('group');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['block_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the group.'));
            }

            $this->dataPersistor->set('group', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
         return $resultRedirect->setPath('*/*/');
    }

    public function filterImageData(array $rawData)
    {
        //Replace icon with fileuploader field name
        $data = $rawData;
        if (isset($data['payment_image'][0]['name'])) {
            $data['payment_image'] = $data['payment_image'][0]['name'];
        } else {
            $data['payment_image'] = null;
        }
        return $data;
    }
}
