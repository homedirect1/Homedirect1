<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Ui\Component;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

/**
 * Class ExportButton
 */
class ExportButton extends \Magento\Ui\Component\AbstractComponent
{
    /**
     * Component name
     */
    const NAME = 'exportButton';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }

    /**
     * @return void
     */
    public function prepare()
    {
        $context = $this->getContext();
        $config = $this->getData('config');
        if (isset($config['options'])) {
            $options = [];
            foreach ($config['options'] as $option) {
                if ($option['value'] != 'xml') {
                    $additionalParams = $this->getAdditionalParams($config, $context);
                    $option['url'] = $this->urlBuilder->getUrl($option['url'], $additionalParams);
                    $options[] = $option;
                }
            }
            $config['options'] = $options;
            $this->setData('config', $config);
        }
        parent::prepare();
    }

    /**
     * Get export button additional parameters
     *
     * @param array $config
     * @param ContextInterface $context
     * @return array
     */
    protected function getAdditionalParams($config, $context)
    {
        $additionalParams = [];
        if (isset($config['additionalParams'])) {
            foreach ($config['additionalParams'] as $paramName => $paramValue) {
                if ('*' == $paramValue) {
                    $paramValue = $context->getRequestParam($paramName);
                }
                $additionalParams[$paramName] = $paramValue;
            }
        }
        return $additionalParams;
    }
}
