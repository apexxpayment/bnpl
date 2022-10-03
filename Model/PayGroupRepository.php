<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Apexx\Bnpl\Api\PayGroupRepositoryInterface;
use Apexx\Bnpl\Api\Data\PayGroupInterface;
use Apexx\Bnpl\Api\Data\PayGroupInterfaceFactory;
use Apexx\Bnpl\Api\Data\PayGroupSearchResultsInterface;
use Apexx\Bnpl\Api\Data\PayGroupSearchResultsInterfaceFactory;
use Apexx\Bnpl\Model\PayGroup;
use Apexx\Bnpl\Model\PayGroupFactory;
use Apexx\Bnpl\Model\ResourceModel\PayGroup as ResourceData;
use Apexx\Bnpl\Model\ResourceModel\PayGroup\Collection;
use Apexx\Bnpl\Model\ResourceModel\PayGroup\CollectionFactory as DataCollectionFactory;


class PayGroupRepository implements PayGroupRepositoryInterface
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var ResourceData
     */
    protected $resource;

    /**
     * @var DataCollectionFactory
     */
    protected $dataCollectionFactory;

    /**
     * @var PayGroupSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var PayGroupInterfaceFactory
     */
    protected $dataInterfaceFactory;

    /**
     * @var PayGroupFactory
     */
    protected $dataModel;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * DataRepository constructor.
     *
     * @param ResourceData $resource
     * @param DataCollectionFactory $dataCollectionFactory
     * @param PayGroupSearchResultsInterfaceFactory $dataSearchResultsInterfaceFactory
     * @param PayGroupInterfaceFactory $dataInterfaceFactory
     * @param PayGroupFactory $dataModel
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceData $resource,
        DataCollectionFactory $dataCollectionFactory,
        PayGroupSearchResultsInterfaceFactory $dataSearchResultsInterfaceFactory,
        PayGroupInterfaceFactory $dataInterfaceFactory,
        PayGroupFactory $dataModel,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->searchResultsFactory = $dataSearchResultsInterfaceFactory;
        $this->dataInterfaceFactory = $dataInterfaceFactory;
        $this->dataModel = $dataModel;
        $this->collectionProcessor = $collectionProcessor ?: $this->getCollectionProcessor();
    }

    /**
     * Save PayGroup
     *
     * @param PayGroupInterface $data
     * @return PayGroupInterface
     * @throws CouldNotSaveException
     */
    public function save(PayGroupInterface $data)
    {
        try {
            /** @var PayGroupInterface|\Magento\Framework\Model\AbstractModel $data */
            $this->resource->save($data);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the data: %1',
                $exception->getMessage()
            ));
        }
        return $data;
    }

    /**
     * Retrieve PayGroup
     *
     * @param int $paygroupId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($paygroupId)
    {
        if (!isset($this->instances[$paygroupId])) {

            /** @var  PayGroup $data */
            $data = $this->dataModel->create();
            $this->resource->load($data, $paygroupId);
            if (!$data->getId()) {
                throw new NoSuchEntityException(__('Requested data doesn\'t exist'));
            }
            $this->instances[$paygroupId] = $data;
        }
        return $this->instances[$paygroupId];
    }

    /**
     * Retrieve paygroup list matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return PayGroupSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Apexx\Bnpl\Model\ResourceModel\PayGroup\Collection $collection */
        $collection = $this->dataCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var PayGroupSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete paygroup
     *
     * @param GroupInterface $data
     * @return bool
     * @throws StateException
     */
    public function delete(PayGroupInterface $data)
    {
        /** @var PayGroupInterface|\Magento\Framework\Model\AbstractModel $data */
        $id = $data->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($data);
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove data %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete paygroup by id
     *
     * @param int $paygroupId
     * @return bool
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function deleteById($paygroupId)
    {
        $data = $this->getById($paygroupId);
        return $this->delete($data);
    }
}
