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

use Magento\Framework\App\Helper\Context;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem;
use Ced\Dropship\Api\DropshipOrderRepositoryInterface;
use Ced\Dropship\Api\Data\DropshipOrderInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception;

/**
 * Class Order, for api integration
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @var \Magento\Catalog\Model\ProductFactory $productFactory
     */
    protected $productFactory;

    /**
     * @var \Ced\Dropship\Helper\Config
     */
    protected $_helper;

    /**
     * @var \Ced\Dropship\Helper\Request
     */
    protected $requestHelper;

    /**
     * @var \Ced\Dripship\Helper\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentRepository
     */
    protected $shipmentRepository;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    protected $shipmentNotifier;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    protected $trackFactory;
    
    /**
     * @var DropshipOrderRepositoryInterface
     */
    protected $dropshipOrderRepo;

    /**
     * @var DropshipOrderInterfaceFactory
     */
    protected $dropshipOrderFactory;
    
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $carrierFactory;
    
    /**
     * Order Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Ced\Dropship\Helper\Config $helper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Ced\Dropship\Helper\Request $requestHelper
     * @param \Ced\Dropship\Helper\Logger $logger
     * @param \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     * @param \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory
     * @param DropshipOrderRepositoryInterface $dropshipOrderRepository
     * @param DropshipOrderInterfaceFactory $dropshipOrderFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\App\ConfigInterface $config,
        \Ced\Dropship\Helper\Config $helper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Ced\Dropship\Helper\Request $requestHelper,
        \Ced\Dropship\Helper\Logger $logger,
        \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        DropshipOrderRepositoryInterface $dropshipOrderRepository,
        DropshipOrderInterfaceFactory $dropshipOrderFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->_helper = $helper;
        $this->productFactory = $productFactory;
        $this->requestHelper = $requestHelper;
        $this->logger = $logger;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->trackFactory = $trackFactory;
        $this->dropshipOrderRepo = $dropshipOrderRepository;
        $this->dropshipOrderFactory = $dropshipOrderFactory;
        $this->orderFactory = $orderFactory;
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * Check if an order is of dropship product
     *
     * @param array $productIds
     * @return array
     */
    public function checkIfDropshipOrder($productIds)
    {
        foreach ($productIds as $productId) {
            $product = $this->productFactory->create()->loadByAttribute("entity_id", $productId);
            if ($product->getData("is_dropship")) {
                return true;
            }
        }
        return false;
    }

    /**
     * Create order on dropship
     *
     * @param array $orderDetails
     */
    public function createOrderOnDropship($orderDetails)
    {
        // echo "<pre>";
        // print_r($orderDetails->getData());
        // die('vishal');

        $order = $this->orderFactory->create()
        ->loadByIncrementId($orderDetails->getCustomerOrderReference());

        $items = $order->getAllItems();
        $productInfo = [];
        $i = 0;
        $price = 0;
        foreach ($items as $item) {
            $productCollection = $this->productFactory->create();
            $product = $productCollection->load($productCollection->getIdBySku($item->getSku()));
            if ((int)$product->getIsDropship() == 1) {
                if ($this->_helper->skuPrefix()) {                    
                    $length = strlen($this->_helper->skuPrefix());                 
                    $skuPre = substr($item->getSku(), 0, $length);
                    if ($skuPre == $this->_helper->skuPrefix()) {
                        $productInfo[$i]['sku'] = substr($item->getSku(), $length);
                    } else {
                        $productInfo[$i]['sku'] = $item->getSku();
                    }
                } else {
                    $productInfo[$i]['sku'] = $item->getSku();
                }                
                $productInfo[$i]['qty'] = $item->getQtyOrdered();
                $i++;
                $price = $item->getPrice() + $price;
            }
        }
        $billingstreet = $order->getBillingAddress()->getStreet();
        if (is_array($billingstreet)) {
            $addressUnited = "";
            foreach ($billingstreet as $adds) {
                $addressUnited .= $adds . " ";
            }

            $billingstreet = $addressUnited;
        }
        $first30 = substr($billingstreet, 0, 30);
        $theRest = substr($billingstreet, 30);

        $custComment = $this->_helper->getCustomerComment();
        if ($custComment == '') {
            $custComment = 'No Comment';
        }
        
        $request = [
            "your_order_no" => $orderDetails->getCustomerOrderReference(), //increament Id
            "first_name" => $order->getBillingAddress()->getFirstName(),
            "last_name"=> $order->getBillingAddress()->getLastName(),
            "address1"=> $first30,
            "address2"=> $theRest,
            "suburb"=> $order->getBillingAddress()->getCity(),
            "state"=> $order->getBillingAddress()->getRegion(),
            "postcode"=> $order->getBillingAddress()->getPostCode(),
            "telephone"=> $order->getBillingAddress()->getTelephone(),
            "comment"=> $custComment,
            "order_items"=> $productInfo,
        ];

        echo "<pre>";
        print_r($request);
        die('VISGKJHJ');

        $response = $this->submitOrder($request);
        try {
            $decodedResponse = json_decode($response, true);
            if (isset($decodedResponse['status']) && $decodedResponse['status'] == -1) {
                throw new LocalizedException(__($decodedResponse['errmsg']));
                // return false;
            } elseif (isset($decodedResponse[0]['status']) && $decodedResponse[0]['status'] == 1) {
                $orderDetails->setOrderDataJson($response);
                $orderDetails->setDropshipOrderId($decodedResponse[0]['serial_number']);
                $orderDetails->setDropshipOrderStatus($decodedResponse[0]['status']);
                $orderDetails->setOrderCreateDropship($decodedResponse[0]['status']);
                $this->dropshipOrderRepo->save($orderDetails);
            }
            return true;
        } catch (LocalizedException $exception) {
            if ($this->_helper->getDebugMode()) {
                $this->logger->warning($exception->getMessage());
                return false;
            }
            return false;
        } catch (Exception $exception) {
            if ($this->_helper->getDebugMode()) {
                $this->logger->error($exception->getMessage());
                return false;
            }
            return false;
        }
    }

    /**
     * Submition of an order to Dropship
     *
     * @param array $orderData
     * @return bool|string
     */
    private function submitOrder($orderData)
    {
        try {
            $url = 'placingOrder';
            $this->requestHelper->setBody(json_encode($orderData));
            $response = $this->requestHelper->sendRequest($url, 'post');
            return $response;
        } catch (Exception $exception) {
            if ($this->_helper->getDebugMode()) {
                $this->logger->error($exception->getMessage());
            }
            return false;
        }
    }

    /**
     * Create an order on Dropship
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function createOrder($order)
    {
        try {
            $dropshipOrder = $this->dropshipOrderFactory->create();
            $dropshipOrder->setCustomer($order->getCustomerName());
            $dropshipOrder->setSalesOrderId($order->getId());
            $dropshipOrder->setDropshipOrderStatus(0);
            $dropshipOrder->setCustomerOrderReference($order->getIncrementId());
            $dropshipOrder->setOrderTotal(0);
            $dropshipOrder->setOrderCreateDropship(0);
            $this->dropshipOrderRepo->save($dropshipOrder);
            return $dropshipOrder;
        } catch (\Exception $exception) {
            if ($this->_helper->getDebugMode()) {
                $this->logger->error($exception->getMessage()."order_increment_id: ".$order->getIncrementId());
            }
        }
    }
}
