<?php


namespace Ced\SmsMsg91\Model;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\Context;

class Send extends AbstractModel
{
    const MSG91_API_URL = 'https://api.msg91.com/api/v5/flow';
    const XML_PATH = 'msg91_sms_gateway/api_setting/';
    const XML_PATH_AUTH_KEY = 'auth_key';
    const XML_PATH_FLOW_ID = 'flow_id';

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfigManager;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    protected $_storeId = 0;

    /**
     * Send constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param StoreManagerInterface $storeManager
     * @param ResourceModel\AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(Context $context,
                                Registry $registry,
                                ScopeConfigInterface $scopeConfigInterface,
                                StoreManagerInterface $storeManager,
                                AbstractResource $resource = null,
                                AbstractDb $resourceCollection = null,
                                array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_scopeConfigManager = $scopeConfigInterface;
        $this->_storeManager = $storeManager;
    }


    /**
     * @param $to
     * @param $otp
     * @return bool
     */
    public function deliverySms($to, $otp,$textmessage='')
    {
        try {
            $postData['mobiles'] = $to;
            $postData['flow_id'] = $this->getFlowId();
            $postData['otp'] = $otp;

            $response = $this->file_get_contents_curl(self::MSG91_API_URL, $postData);
            $arrayResponse = json_decode($response, TRUE);
            if (isset($arrayResponse['type']) && $arrayResponse['type'] == 'success') {
                return true;
            }
        } catch (Exception $e) {
            $this->_set('property',$e->getMessage());
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getFlowId()
    {
        return $this->_scopeConfigManager->getValue('sms_gateway/msg91/flow_id', ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }

    /**
     * @return mixed
     */
    public function getStore()
    {
        if ($this->_storeId) $storeId = (int)$this->_storeId;
        else $storeId = isset($_REQUEST['store']) ? (int)$_REQUEST['store'] : null;
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @param $url
     * @param $postData
     * @return bool|string
     */
    public function file_get_contents_curl($url, $postData)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                "authkey: " . $this->getAuthKey(),
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * @return mixed
     */
    public function getAuthKey()
    {
        return $this->_scopeConfigManager->getValue('sms_gateway/msg91/auth_key', ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }
    
     public function _get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    public function _set($property, $value)
    {
            $this->$property = $value;
    }
}
