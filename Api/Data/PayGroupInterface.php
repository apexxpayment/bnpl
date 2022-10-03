<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Api\Data;


interface PayGroupInterface
{
    /**
     * Pay Group entity data keys.
     */
    const ID = 'id';
    const PAYMENT_TITLE = 'payment_title';
    const PAYMENT_CODE= 'payment_code';
    const PAYMENT_TYPE = 'payment_type';
    const PAYMENT_ACTION = 'payment_action';
    const PAYMENT_IMAGE = 'payment_image';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get paygroup id.
     *
     * @api
     * @return int
     */
    public function getId();

    /**
     * Set paygroup id.
     *
     * @api
     * @param int $id
     * @return PayGroupInterface
     */
    public function setId($id);

    /**
     * Get paygroup title
     *
     * @api
     * @return string
     */
    public function getPaymentTitle();

    /**
     * Set paygroup title
     *
     * @api
     * @param string $paymentTitle
     * @return PayGroupInterface
     */
    public function setPaymentTitle($paymentTitle);

    /**
     * Get paygroup code
     *
     * @api
     * @return string
     */
    public function getPaymentCode();

    /**
     * Set paygroup code
     *
     * @api
     * @param string $paymentCode
     * @return PayGroupInterface
     */
    public function setPaymentCode($paymentCode);

    /**
     * Get paygroup type
     *
     * @api
     * @return string
     */
    public function getPaymentType();

    /**
     * Set paygroup type
     *
     * @api
     * @param string $paymentType
     * @return PayGroupInterface
     */
    public function setPaymentType($paymentType);

    /**
     * Get payment action
     *
     * @api
     * @return string
     */
    public function getPaymentAction();

    /**
     * Set paygroup action
     *
     * @api
     * @param string $paymentAction
     * @return PayGroupInterface
     */
    public function setPaymentAction($paymentAction);

    /**
     * Get paygroup image
     *
     * @api
     * @return string
     */
    public function getPaymentImage();

    /**
     * Set paygroup image
     *
     * @api
     * @param string $paymentImage
     * @return PayGroupInterface
     */
    public function setPaymentImage($paymentImage);

    /**
     * Get paygroup status
     *
     * @api
     * @return string
     */
    public function getStatus();

    /**
     * Set paygroup status
     *
     * @api
     * @param string $status
     * @return PayGroupInterface
     */
    public function setStatus($status);

    /**
     * Get paygroup created at
     *
     * @api
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set paygroup created at
     *
     * @api
     * @param string $createdAt
     * @return PayGroupInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get paygroup updated at
     *
     * @api
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set paygroup updated at
     *
     * @api
     * @param string $updatedAt
     * @return PayGroupInterface
     */
    public function setUpdatedAt($updatedAt);
}
