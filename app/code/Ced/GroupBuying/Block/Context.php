<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Block;

use Ced\GroupBuying\Helper\Data;
use Ced\GroupBuying\Model\Config;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Escaper;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\Template\File\Resolver;
use Magento\Framework\View\Element\Template\File\Validator;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\TemplateEnginePool;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract product block context
 */
class Context extends \Magento\Framework\View\Element\Template\Context
{

    /**
     * @var Data
     */
    protected $_devToolHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var UrlFactory
     */
    protected $_urlFactory;


    /**
     * Constructor
     *
     * @param RequestInterface                                   $request
     * @param LayoutInterface                                    $layout
     * @param ManagerInterface                                   $eventManager
     * @param UrlInterface                                       $urlBuilder
     * @param CacheInterface                                     $cache
     * @param DesignInterface                                    $design
     * @param SessionManagerInterface                            $session
     * @param SidResolverInterface                               $sidResolver
     * @param ScopeConfigInterface                               $scopeConfig
     * @param Repository                                         $assetRepo
     * @param \Magento\Framework\View\ConfigInterface            $viewConfig
     * @param StateInterface                                     $cacheState
     * @param LoggerInterface                                    $logger
     * @param Escaper                                            $escaper
     * @param FilterManager                                      $filterManager
     * @param TimezoneInterface                                  $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Filesystem                                         $filesystem
     * @param \Magento\Framework\View\FileSystem                 $viewFileSystem
     * @param TemplateEnginePool                                 $enginePool
     * @param State                                              $appState
     * @param StoreManagerInterface                              $storeManager
     * @param \Magento\Framework\View\Page\Config                $pageConfig
     * @param Resolver                                           $resolver
     * @param Validator                                          $validator
     * @param Data                                               $devToolHelper
     * @param \Magento\Framework\Registry                        $registry
     * @param Config                                             $config
     * @param ObjectManagerInterface                             $objectManager
     * @param UrlFactory                                         $urlFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        RequestInterface $request,
        LayoutInterface $layout,
        ManagerInterface $eventManager,
        UrlInterface $urlBuilder,
        CacheInterface $cache,
        DesignInterface $design,
        SessionManagerInterface $session,
        SidResolverInterface $sidResolver,
        ScopeConfigInterface $scopeConfig,
        Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        StateInterface $cacheState,
        LoggerInterface $logger,
        Escaper $escaper,
        FilterManager $filterManager,
        TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        Filesystem $filesystem,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        TemplateEnginePool $enginePool,
        State $appState,
        StoreManagerInterface $storeManager,
        \Magento\Framework\View\Page\Config $pageConfig,
        Resolver $resolver,
        Validator $validator,
        Data $devToolHelper,
        \Magento\Framework\Registry $registry,
        Config $config,
        ObjectManagerInterface $objectManager,
        UrlFactory $urlFactory
    ) {
        $this->_devToolHelper = $devToolHelper;
        $this->registry       = $registry;
        $this->_config        = $config;
        $this->_objectManager = $objectManager;
        $this->_urlFactory    = $urlFactory;
        parent::__construct(
            $request,
            $layout,
            $eventManager,
            $urlBuilder,
            $cache,
            $design,
            $session,
            $sidResolver,
            $scopeConfig,
            $assetRepo,
            $viewConfig,
            $cacheState,
            $logger,
            $escaper,
            $filterManager,
            $localeDate,
            $inlineTranslation,
            $filesystem,
            $viewFileSystem,
            $enginePool,
            $appState,
            $storeManager,
            $pageConfig,
            $resolver,
            $validator
        );

    }//end __construct()


    /**
     * Function for getting developer helper object
     *
     * @return Data
     */
    public function getGroupBuyingHelper(): Data
    {
        return $this->_devToolHelper;

    }//end getGroupBuyingHelper()


    /**
     * Function for getting registry object
     *
     * @return \Magento\Framework\Registry
     */
    public function getRegistry(): \Magento\Framework\Registry
    {
        return $this->registry;

    }//end getRegistry()


    /**
     * Function for getting groupbuying model config object
     *
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->_config;

    }//end getConfig()


    /**
     * Function for getting object manager object
     *
     * @return ObjectManagerInterface
     */
    public function getObjectManager(): ObjectManagerInterface
    {
        return $this->_objectManager;

    }//end getObjectManager()


    /**
     * Function for getting UrlFactory object
     *
     * @return UrlFactory
     */
    public function getUrlFactory(): UrlFactory
    {
        return $this->_urlFactory;

    }//end getUrlFactory()


}//end class
