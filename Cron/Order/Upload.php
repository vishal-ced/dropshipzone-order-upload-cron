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

namespace Ced\Dropship\Cron\Order;

class Upload
{
    /**
     * Chunck size
     */
    const CHUNK_SIZE = 2;

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
     * @var Order
     */
    protected $orderHelper;

    /**
     * UpdateProduct constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ced\Dropship\Helper\Config $helper
     * @param \Ced\Dropship\Helper\Product $productHelper
     * @param \Ced\Dropship\Helper\Request $requestHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ced\Dropship\Helper\Order $orderHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Ced\Dropship\Helper\Config $helper,
        \Ced\Dropship\Helper\Product $productHelper,
        \Ced\Dropship\Helper\Request $requestHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Psr\Log\LoggerInterface $logger,
        \Ced\Dropship\Model\ResourceModel\DropshipOrder\CollectionFactory $orderCollection,
        \Ced\Dropship\Helper\Order $orderHelper,
    ) {
        // parent::__construct($this->context);
        $this->helper = $helper;
        $this->productHelper = $productHelper;
        $this->requestHelper = $requestHelper;
        $this->logger = $logger;
        $this->productCollection = $productCollection;
        $this->orderCollection = $orderCollection;
        $this->orderHelper = $orderHelper;
    }
    
    /**
     * Execute function to run cron
     */
     
    public function execute()
    {
        try {
            if ($this->helper->enableOrderCron() == 1) {
                $batchId = $this->helper->getBatch();
                $orderCollection = $this->orderCollection->create()
                ->addFieldToFilter('is_created_dropship', ['eq' => 0]);

                $page_no = $orderCollection->getSize();
                // $orderCollection->getSelect()->limit(10);
                $orderCollection->setCurPage($batchId);
                $orderCollection->setPageSize(self::CHUNK_SIZE);
                
                if ($orderCollection->getSize() > 0) {
                    foreach ($orderCollection as $order) {
                        $orderHelper= $this->orderHelper->createOrderOnDropship($order);
                    }
                }

                $batchId = (int)$batchId + 1;
                if ((int)$batchId > (int)$page_no) {
                    $batchId = 1;
                }
                $this->helper->setBatch($batchId);
            }
        } catch (Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            $this->logger->addError($e->getMessage(), __METHOD__);
        }
    }
}