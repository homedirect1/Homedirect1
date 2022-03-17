<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Model;

use Webkul\Recurring\Api\Data\PlansInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Webkul Recurring Plans Model
 */
class SubscriptionType extends \Magento\Framework\Model\AbstractModel implements PlansInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**#@+
     * Plan's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * Recurring plans cache tag
     */
    const CACHE_TAG = 'recurring_product_plans';

    /**
     * @var string
     */
    protected $_cacheTag = 'recurring_product_plans';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Webkul\Recurring\Model\ResourceModel\SubscriptionType::class);
    }

    /**
     * Load object data
     *
     * @param int|null $id
     * @param string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRoutePlans();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route
     *
     * @return \Webkul\Recurring\Model\Plans
     */
    public function noRoutePlans()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Recurring\Api\Data\PlansInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
