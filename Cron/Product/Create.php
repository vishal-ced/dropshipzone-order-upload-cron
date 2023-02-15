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

namespace Ced\Dropship\Cron\Product;

class Create
{
    /**
     * Chunck size
     */
    const CHUNK_SIZE = 40;

    /**
     * @var \Ced\Dropship\Helper\Config
     */
    protected $helper;

    /**
     * @var \Ced\Dropship\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Ced\Dropship\Helper\Request
     */
    protected $requestHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollection;

    /**
     * UpdateProduct constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ced\Dropship\Helper\Config $helper
     * @param \Ced\Dropship\Helper\Product $productHelper
     * @param \Ced\Dropship\Helper\Request $requestHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Ced\Dropship\Helper\Config $helper,
        \Ced\Dropship\Helper\Product $productHelper,
        \Ced\Dropship\Helper\Request $requestHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Psr\Log\LoggerInterface $logger
    ) {
        // parent::__construct($this->context);
        $this->helper = $helper;
        $this->productHelper = $productHelper;
        $this->requestHelper = $requestHelper;
        $this->logger = $logger;
        $this->productCollection = $productCollection;
    }
    
    /**
     * Execute function to run cron
     */
    public function execute()
    {
        try {
            if ($this->helper->enableCron() == 1) {
                $batchId = $this->helper->getBatch();
                $url = "products?page_no=$batchId&limit=".self::CHUNK_SIZE;
                $res = $this->requestHelper->sendRequest($url, 'get');
                $res = json_decode($res);
                if (property_exists($res, 'code')) {
                    return $this;
                }
                $page_no = $res->total_pages;
                $products = $res->result;
                foreach ($products as $new_product) {
                    if ($this->helper->createOutOfStockProducts() == 0) {
                        if ((int)$new_product->stock_qty <= 0) {
                            $response = "Can't create product, product Out of stock.";
                        } else {
                            $response = $this->productHelper->createProduct($new_product);
                        }
                    } else {
                        $response = $this->productHelper->createProduct($new_product);
                    }
                }
                $batchId = (int)$batchId + 1;
                if ((int)$batchId > (int)$page_no) {
                    $batchId = 1;
                }
                $this->helper->setBatch($batchId);
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shikkhar.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($batchId);
                return $this;
            } else {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test2.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($batchId);
                return $this;
            }
        } catch (Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
