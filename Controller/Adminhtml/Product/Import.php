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

namespace Ced\Dropship\Controller\Adminhtml\Product;

use Ced\Dropship\Helper\Logger;

/**
 * Class Import to import Product by chunks
 */
class Import extends \Magento\Backend\App\Action
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
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJson;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * Import constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ced\Dropship\Helper\Config $helper
     * @param \Ced\Dropship\Helper\Product $productHelper
     * @param \Ced\Dropship\Helper\Request $requestHelper
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Backend\Model\Auth\Session $session
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJson
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ced\Dropship\Helper\Config $helper,
        \Ced\Dropship\Helper\Product $productHelper,
        \Ced\Dropship\Helper\Request $requestHelper,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\File\Csv $csv,
        \Magento\Backend\Model\Auth\Session $session,
        \Magento\Framework\Controller\Result\JsonFactory $resultJson,
        \Ced\Dropship\Helper\Logger $logger
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->productHelper = $productHelper;
        $this->requestHelper = $requestHelper;
        $this->csv = $csv;
        $this->session = $session;
        $this->resultJson = $resultJson;
        $this->logger = $logger;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Execute function
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $batchId = $this->getRequest()->getParam('batchid');
            if ($batchId != "") {
                // $allproducts = $this->session->getchunkedProduct();
                // $products = $allproducts[$batchId];
                $url = "products?page_no=$batchId&limit=".self::CHUNK_SIZE;
                $res = $this->requestHelper->sendRequest($url, 'get');
                $res = json_decode($res);
                $products = $res->result;
                foreach ($products as $new_product) {
                    if ($this->helper->createOutOfStockProducts() == 0) {
                        if ((int)$new_product->stock_qty <= 0) {
                            $response = $this->productHelper->createProduct($new_product);
                        } else {
                            $response = $this->productHelper->createProduct($new_product);
                        }
                    } else {
                        $response = $this->productHelper->createProduct($new_product);
                    }
                }
                /** @var \Magento\Framework\Controller\Result\Json $result */
                $result = $this->resultJson->create();
                return $result->setData(
                    [
                        'success' => 'Created products',
                        'messages' => $response
                    ]
                );
            }
            $url = 'products?page_no=1&limit='.self::CHUNK_SIZE;
            $result = $this->resultJson->create();
            $res = $this->requestHelper->sendRequest($url, 'get');
            $res = json_decode($res);
            return $result->setData($res->total_pages);
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage(__("Something went wrong while Importing product(s)."));
            $this->logger->error($exception->getMessage(), [
                'method' => __METHOD__
            ]);
        }
    }
}
