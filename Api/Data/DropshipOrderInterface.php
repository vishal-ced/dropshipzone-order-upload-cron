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

namespace Ced\Dropship\Api\Data;

use Magento\Customer\Model\Customer;
use Magento\Framework\Stdlib\DateTime;

/**
 * Interface DropshipOrderInterface
 */
interface DropshipOrderInterface
{
    /**
     * The Dropship Order Table Name
     */
    const TABLE_NAME = 'ced_dropship_order';

    /**
     * Primary key
     */
    const COLUMN_ID = 'id';

    /**
     * Customer Id for the order
     */
    const CUSTOMER_NAME = 'customer_name';

    /**
     * Status of order on dropship
     */
    const DROPSHIP_ORDER_STATUS = 'dropship_order_status';

    /**
     * Customer reference number mostly will be the incremental id
     */
    const CUSTOMER_ORDER_REFERENCE = 'customer_order_reference';

    /**
     * If dropship order was created
     */
    const IS_DROPSHIP_CREATED = 'is_created_dropship';

    /**
     * Shipping tracking details like Tracking number
     */
    const SHIPPING_TRACKING = 'shipping_tracking';

    /**
     * Will hold the completed object of response from order api call
     */
    const JSON_DATA = 'json_data';

    /**
     * The total of the order
     */
    const TOTAL = 'total';

    /**
     * The date of creation
     */
    const CREATED_AT = 'created_at';

    /**
     * The date of updation
     */
    const UPDATED_AT = 'updated_at';

    /**
     * Order Id of the sales order
     */
    const SALES_ORDER_ID = 'sales_order_id';

    /**
     * Order Id from dropship Order Id
     */
    const DROPSHIP_ORDER_ID = 'dropship_order_id';

    /**
     * Invoice Url from Vixal
     */
    const DROPSHIP_INVOICE_URL = 'dropship_invoice_url';

    /**
     * Get the customer id of an order
     *
     * @return string
     */
    public function getCustomer();

    /**
     * Set the customer for an order
     *
     * @param string $customer
     * @return $this
     */
    public function setCustomer($customer);

    /**
     * Status of the dropship order
     *
     * @return string
     */
    public function getDropshipOrderStatus();

    /**
     * Set a Dropship Order status
     *
     * @param string $status
     * @return $this
     */
    public function setDropshipOrderStatus($status);

    /**
     * Get Customer order refernce "Order Incremental Id"
     *
     * @return string
     */
    public function getCustomerOrderReference();

    /**
     * Set customer Order refence Id
     *
     * @param string $salesOrderId
     * @return $this
     */
    public function setCustomerOrderReference($salesOrderId);

    /**
     * Set the order if created or not on Dropship
     *
     * @return string
     */
    public function getOrderCreateDropship();

    /**
     * Get status if order is created on Dropship
     *
     * @param string $status
     * @return $this
     */
    public function setOrderCreateDropship($status);

    /**
     * Set Sales Order Id
     *
     * @param string $orderId
     * @return $this
     */
    public function setSalesOrderId($orderId);

    /**
     * Get Shipping Tracking details
     *
     * @return string
     */
    public function getShippingTrackingDetails();

    /**
     * Set the shipping details
     *
     * @param string $trackingDetails
     * @return $this
     */
    public function setShippingTracking($trackingDetails);

    /**
     * Get Order data in Json data object
     *
     * @return string
     */
    public function getOrderDataJson();

    /**
     * Set the order data
     *
     * @param string $jsonData
     * @return $this
     */
    public function setOrderDataJson($jsonData);

    /**
     * Get the order total
     *
     * @return string
     */
    public function getOrderTotal();

    /**
     * Set Order total
     *
     * @param string $total
     * @return $this
     */
    public function setOrderTotal($total);

    /**
     * Get created date
     *
     * @return DateTime
     */
    public function getCreatedDate();

    /**
     * Get updated date
     *
     * @return DateTime
     */
    public function getUpdatedDate();

    /**
     * Get Sales order Id
     *
     * @return string
     */
    public function getSalesOrderId();

    /**
     * Get Dropship Order Id
     *
     * @return string
     */
    public function getDropshipOrderId();

    /**
     * Set Dropship Order Id
     *
     * @param string $orderDropshipId
     * @return $this
     */
    public function setDropshipOrderId($orderDropshipId);

    /**
     * Get the Invoice url
     *
     * @return string
     */
    public function getDropshipInvoiceUrl();

    /**
     * Set the Dropship Invoice Url
     *
     * @param string $url
     * @return $this
     */
    public function setDropshipInvoiceUrl($url);
}
