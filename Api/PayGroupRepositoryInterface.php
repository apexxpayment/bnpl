<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Apexx\Bnpl\Api\Data\PayGroupInterface;


interface PayGroupRepositoryInterface
{
    /**
     * Save PayGroup
     *
     * @param PayGroupInterface $paygroupDataObject
     * @return PayGroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(PayGroupInterface $paygroupDataObject);

    /**
     * Retrieve PayGroup
     *
     * @api
     * @param int $paygroupId
     * @return PayGroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($paygroupId);

    /**
     * Retrieve PayGroup list matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Apexx\bnpl\Api\Data\PayGroupSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete paygroup
     *
     * @api
     * @param PayGroupInterface $data
     * @return  bool
     */
    public function delete(PayGroupInterface $data);

    /**
     * Delete paygroup by ID
     *
     * @api
     * @param int $paygroupId
     * @return bool
     */
    public function deleteById($paygroupId);
}
