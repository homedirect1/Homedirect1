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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Block\Adminhtml\Account\Banner;

/**
 * Class Images
 * @package Ced\Affiliate\Block\Adminhtml\Account\Banner
 */
class Images extends \Magento\Backend\Block\Template
{
    /**
     * Rating summary template name
     *
     * @var string
     */
    protected $_template = 'Ced_Affiliate::affiliate/banner/images.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */

    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * Images constructor.
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->filesystem = $filesystem;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getBanner()
    {
        return $this->_coreRegistry->registry('current_banner');
    }

    /**
     * @return \Magento\Framework\Filesystem\Directory\Read|string
     */
    public function getMediaDirectory()
    {
        $mediaDirectory = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = $mediaDirectory->getAbsolutePath('banner/files/');
        return $path;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public Function getImageSrc()
    {
        $url = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'banner/files/';
        return $url;
    }
}
