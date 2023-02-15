<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_Dropship
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Dropship\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection as OrderStatusCollection;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Config to get all configurations
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    const CUSTOMER_EMAIL = 'dropship/general/customerEmail';
    const CUSTOMER_PASS = 'dropship/general/password';
    const DEBUG_MODE = 'dropship/general/debug';
    
    const CREATE_CATEGORY = 'dropship/product/category';
    const CREATE_PRODUCTS_IN_DISABLE = 'dropship/product/productindidable';
    const CREATE_OUT_OF_STOCK_PRODUCT = 'dropship/product/productoutofstock';
    const SKU_PREFIX = 'dropship/product/skuPrefix';
    const PERCENT_PRICE = 'dropship/product/IncPer';
    const FIXED_PRICE = 'dropship/product/IncFix';

    const CREATE_ORDER_WHEN = 'dropship/order/createOrderWhen';
    const CUSTOMER_COMMENT = 'dropship/order/cumstomer_comment';

    const CRON_ENABLE = 'dropship/cron/productCron';
    const ORDER_CRON_ENABLE = 'dropship/cron/orderUploadCron';
    const CRON_BATCH = 'dropship/cron/batch';

    /**
     * Data Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface $_storeManager
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Config\Model\ResourceModel\Config $_resourceConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\App\ConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $_storeManager,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config $_resourceConfig
    ) {
        $this->config = $config;
        parent::__construct($context);
        $this->_dir = $dir;
        $this->_storeManager = $_storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_resourceConfig = $_resourceConfig;
    }

    /**
     * To getStoreid to get store id
     *
     * @return string
     */
    public function getStoreid()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * To getCustomerEmail Get Customer Email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->scopeConfig->getValue(
            self::CUSTOMER_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To getCustomerPassword Get Customer Password
     *
     * @return string
     */
    public function getCustomerPassword()
    {
        return $this->scopeConfig->getValue(
            self::CUSTOMER_PASS,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To getDebugMode, Get debug mode enable or disable
     *
     * @return bool
     */
    public function getDebugMode()
    {
        return $this->scopeConfig->getValue(
            self::DEBUG_MODE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To getCreateCategory, To create category or not
     *
     * @return bool
     */
    public function getCreateCategory()
    {
        return $this->scopeConfig->getValue(
            self::CREATE_CATEGORY,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To createProductInDisableState, Get create product in disable state
     *
     * @return bool
     */
    public function createProductInDisableState()
    {
        return $this->scopeConfig->getValue(
            self::CREATE_PRODUCTS_IN_DISABLE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To createOutOfStockProducts, Create product with 0 qty
     *
     * @return bool
     */
    public function createOutOfStockProducts()
    {
        return $this->scopeConfig->getValue(
            self::CREATE_OUT_OF_STOCK_PRODUCT,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * Get Media Path
     *
     * @return string
     */
    public function getMediaPath()
    {
        return $this->_dir->getPath('media');
    }

    /**
     * To skuPrefix, sku prefix
     *
     * @return string
     */
    public function skuPrefix()
    {
        return $this->scopeConfig->getValue(
            self::SKU_PREFIX,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To incPriceInPercent, increase price in percent
     *
     * @return string
     */
    public function incPriceInPercent()
    {
        return $this->scopeConfig->getValue(
            self::PERCENT_PRICE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To incFixedPrice, increase fixed price
     *
     * @return string
     */
    public function incFixedPrice()
    {
        return $this->scopeConfig->getValue(
            self::FIXED_PRICE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To createOrderWhen, when to create order (events)
     *
     * @return string
     */
    public function createOrderWhen()
    {
        return $this->scopeConfig->getValue(
            self::CREATE_ORDER_WHEN,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * To getCustomerComment, To get customer comment
     *
     * @return string
     */
    public function getCustomerComment()
    {
        return $this->scopeConfig->getValue(
            self::CUSTOMER_COMMENT,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }
    
    /**
     * Get cron enable or disable, when to create order (events)
     *
     * @return string
     */
    public function enableCron()
    {
        return $this->scopeConfig->getValue(
            self::CRON_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * Get cron enable or disable, when to upload order (events)
     *
     * @return string
     */
    public function enableOrderCron()
    {
        return $this->scopeConfig->getValue(
            self::ORDER_CRON_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }

    /**
     * Set batch Value
     *
     * @param string $val
     */
    public function setBatch($val)
    {
        $this->_resourceConfig->saveConfig(
            self::CRON_BATCH,
            $val,
            $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );
    }

    /**
     * Get batch value
     *
     * @return string
     */
    public function getBatch()
    {
        return $this->scopeConfig->getValue(
            self::CRON_BATCH,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
    }
}
