<?php
/**
 * Custom payment method in Magento 2
 * @category    Bnpl
 * @package     Apexx_Bnpl
 */

namespace Apexx\Bnpl\Block\Adminhtml\PayGroup\Edit;

use Magento\Backend\Block\Widget\Context;
use Apexx\Bnpl\Model\PayGroupFactory;
use Apexx\Bnpl\Model\ResourceModel\PayGroup;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

class GenericButton
{
    /**
     * @var PayGroupFactory
     */
    protected $paygroupFactory;

    /*
     * @var PayGroup
     */
    protected $paygroupResourceModel;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @param PayGroupFactory $paygroupFactory
     * @param PayGroup $paygroupResourceModel
     */
    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request,
        PayGroupFactory $paygroupFactory,
        PayGroup $paygroupResourceModel
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->paygroupFactory = $paygroupFactory;
        $this->paygroupResourceModel = $paygroupResourceModel;
    }

    /**
     * Return paygroup id
     *
     * @return int|null
     */
    public function getId()
    {
        $group = $this->paygroupFactory->create();

        $this->paygroupResourceModel->load(
            $group,
            $this->request->getParam('id')
        );

        return $group->getId() ?: null;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
