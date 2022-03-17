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
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Model\Vsettings\Shipping\Methods;

use Ced\CsMultiShipping\Model\Vsettings\Shipping\Methods\AbstractModel;
use Ced\DomesticAustralianShipping\Helper\Config;
use Magento\Directory\Model\Config\Source\CountryFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Storepickup
 * @package Ced\CsStorePickup\Model\Vsettings\Shipping\Methods
 */
class Storepickup extends AbstractModel
{
    /**
     * @var string
     */
    protected $_code = 'storepickupshipping';

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @var string
     */
    protected $_codeSeparator = '-';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var CountryFactory
     */
    protected $_countryFactory;

    /**
     * Retreive input fields
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param CountryFactory $countryFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        CountryFactory $countryFactory,
        array $data = []
    )
    {
        $this->_countryFactory = $countryFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return array|mixed
     */
    public function getFields()
    {
        $fields['active'] = array('type' => 'select',
            'required' => true,
            'values' => array(
                array('label' => __('Yes'), 'value' => 1),
                array('label' => __('No'), 'value' => 0)
            )
        );

        $fields['title'] = array('type' => 'text', 'required' => true);
        $fields['method_name'] = array('type' => 'text', 'required' => true);
        $fields['store_price'] = array('type' => 'text', 'required' => true);


        $alloptions = $this->_countryFactory->create()->toOptionArray();

        if ($this->_scopeConfig->getValue('carriers/storepickup/sallowspecific',
            ScopeInterface::SCOPE_STORE)) {


            $availableCountries = explode(',', $this->_scopeConfig->getValue(
                'carriers/storepickup/specificcountry', ScopeInterface::SCOPE_STORE));
            foreach ($alloptions as $key => $value) {
                if (in_array($value['value'], $availableCountries)) {
                    $allcountry[] = $value;
                }
            }
        } else {
            $allcountry = $alloptions;
        }

        $fields['allowed_country'] = array('type' => 'multiselect',
            'values' => $allcountry
        );
        return $fields;
    }

    /**
     * Retreive labels
     *
     * @param string $key
     * @return string
     */
    public function getLabel($key)
    {
        switch ($key) {
            case 'label' :
                return __('Store Pickup');
                break;
            case 'title' :
                return __('Title');
                break;
            case 'method_name' :
                return __('Method Name');
                break;
            case 'store_price' :
                return __('Shipping Price');
                break;
            case 'allowed_country':
                return __('Allowed Country');
                break;
            case 'active':
                return __('Active');
                break;
            default :
                return parent::getLabel($key);
                break;
        }
    }

}