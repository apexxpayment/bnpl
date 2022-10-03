<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Model;

use Magento\Framework\Model\AbstractModel;
use Apexx\Bnpl\Api\Data\PayGroupInterface;
use Apexx\Bnpl\Model\ResourceModel\PayGroup as ResourceData;


class PayGroup extends AbstractModel implements PayGroupInterface
{
    /**
     * Initialise resource model
     */
    protected function _construct()
    {
        $this->_init(ResourceData::class);
    }

    /**
     * Get paygroup id.
     *
     * @api
     * @return int
     */
    public function getId()
    {
        return $this->getData(PayGroupInterface::ID);
    }

    /**
     * Set paygroup id.
     *
     * @api
     * @param int $id
     * @return PayGroupInterface
     */
    public function setId($id)
    {
        return $this->setData(PayGroupInterface::ID, $id);
    }

    /**
     * Get paygroup title
     *
     * @api
     * @return string
     */
    public function getPaymentTitle()
    {
        return $this->getData(PayGroupInterface::PAYMENT_TITLE);
    }

    /**
     * Set paygroup title
     *
     * @api
     * @param string $paymentTitle
     * @return PayGroupInterface
     */
    public function setPaymentTitle($paymentTitle)
    {
        return $this->setData(PayGroupInterface::ID, $paymentTitle);
    }

    /**
     * Get paygroup code
     *
     * @api
     * @return string
     */
    public function getPaymentCode()
    {
        return $this->getData(PayGroupInterface::PAYMENT_CODE);
    }

    /**
     * Set paygroup code
     *
     * @api
     * @param string $paymentCode
     * @return PayGroupInterface
     */
    public function setPaymentCode($paymentCode)
    {
        return $this->setData(PayGroupInterface::ID, $paymentCode);
    }

    /**
     * Get paygroup type
     *
     * @api
     * @return string
     */
    public function getPaymentType()
    {
        return $this->getData(PayGroupInterface::PAYMENT_TYPE);
    }

    /**
     * Set paygroup type
     *
     * @api
     * @param string $paymentType
     * @return PayGroupInterface
     */
    public function setPaymentType($paymentType)
    {
        return $this->setData(PayGroupInterface::ID, $paymentType);
    }

    /**
     * Get payment action
     *
     * @api
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->getData(PayGroupInterface::PAYMENT_ACTION);
    }

    /**
     * Set paygroup action
     *
     * @api
     * @param string $paymentAction
     * @return PayGroupInterface
     */
    public function setPaymentAction($paymentAction)
    {
        return $this->setData(PayGroupInterface::ID, $paymentAction);
    }

    /**
     * Get paygroup image
     *
     * @api
     * @return string
     */
    public function getPaymentImage()
    {
        return $this->getData(PayGroupInterface::PAYMENT_IMAGE);
    }

    /**
     * Set paygroup code
     *
     * @api
     * @param string $paymentImage
     * @return PayGroupInterface
     */
    public function setPaymentImage($paymentImage)
    {
        return $this->setData(PayGroupInterface::ID, $paymentImage);
    }

    /**
     * Get status
     *
     * @api
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(PayGroupInterface::STATUS);
    }

    /**
     * Set status
     *
     * @api
     * @param string $status
     * @return PayGroupInterface
     */
    public function setStatus($status)
    {
        return $this->setData(PayGroupInterface::ID, $status);
    }

    /**
     * Get paygroup created at
     *
     * @api
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(PayGroupInterface::CREATED_AT);
    }

    /**
     * Set paygroup created at
     *
     * @api
     * @param string $createdAt
     * @return PayGroupInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(PayGroupInterface::ID, $createdAt);
    }

    /**
     * Get paygroup updated at
     *
     * @api
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(PayGroupInterface::UPDATED_AT);
    }

    /**
     * Set paygroup updated at
     *
     * @api
     * @param string $updatedAt
     * @return PayGroupInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(PayGroupInterface::ID, $updatedAt);
    }
}
