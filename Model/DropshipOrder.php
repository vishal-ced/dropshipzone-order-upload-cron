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

namespace Ced\Dropship\Model;

use Ced\Dropship\Api\Data\DropshipOrderInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Model\Context;

/**
 * Class DropshipOrder getter and setter
 */
class DropshipOrder extends \Magento\Framework\Model\AbstractModel implements DropshipOrderInterface
{
    /**
     * Standard model initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(\Ced\Dropship\Model\ResourceModel\DropshipOrder::class);
    }

    /**
     * Gets the Tabel id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(DropshipOrderInterface::COLUMN_ID);
    }

    /**
     * Get the customer of an order
     *
     * @return string
     */
    public function getCustomer()
    {
        return $this->getData(DropshipOrderInterface::CUSTOMER_NAME);
    }

    /**
     * Set the customer for an order
     *
     * @param string $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        return $this->setData(DropshipOrderInterface::CUSTOMER_NAME, $customer);
    }

    /**
     * Status of the Dropship order
     *
     * @return string
     */
    public function getDropshipOrderStatus()
    {
        return $this->getData(DropshipOrderInterface::DROPSHIP_ORDER_STATUS);
    }

    /**
     * Set a Dropship Order status
     *
     * @param string $status
     * @return $this
     */
    public function setDropshipOrderStatus($status)
    {
        return $this->setData(DropshipOrderInterface::DROPSHIP_ORDER_STATUS, $status);
    }

    /**
     * Get Customer order refernce "Order Incremental Id"
     *
     * @return string
     */
    public function getCustomerOrderReference()
    {
        return $this->getData(DropshipOrderInterface::CUSTOMER_ORDER_REFERENCE);
    }

    /**
     * Set customer Order refence Id
     *
     * @param string $salesOrderId
     * @return $this
     */
    public function setCustomerOrderReference($salesOrderId)
    {
        return $this->setData(DropshipOrderInterface::CUSTOMER_ORDER_REFERENCE, $salesOrderId);
    }

    /**
     * Get Shipping Tracking details
     *
     * @return string
     */
    public function getShippingTrackingDetails()
    {
        return $this->getData(DropshipOrderInterface::SHIPPING_TRACKING);
    }

    /**
     * Set the shipping details
     *
     * @param string $trackingDetails
     * @return $this
     */
    public function setShippingTracking($trackingDetails)
    {
        return $this->setData(DropshipOrderInterface::SHIPPING_TRACKING, $trackingDetails);
    }

    /**
     * Get Order data in Json data object
     *
     * @return string
     */
    public function getOrderDataJson()
    {
        return $this->getData(DropshipOrderInterface::JSON_DATA);
    }

    /**
     * Set the order data
     *
     * @param string $jsonData
     * @return $this
     */
    public function setOrderDataJson($jsonData)
    {
        return $this->setData(DropshipOrderInterface::JSON_DATA, $jsonData);
    }

    /**
     * Get the order total
     *
     * @return string
     */
    public function getOrderTotal()
    {
        return $this->getData(DropshipOrderInterface::TOTAL);
    }

    /**
     * Set Order total
     *
     * @param string $total
     * @return $this
     */
    public function setOrderTotal($total)
    {
        return $this->setData(DropshipOrderInterface::TOTAL, $total);
    }

    /**
     * Get created date
     *
     * @return DateTime
     */
    public function getCreatedDate()
    {
        return $this->getData(DropshipOrderInterface::CREATED_AT);
    }

    /**
     * Get Updated date
     *
     * @return DateTime
     */
    public function getUpdatedDate()
    {
        return $this->getData(DropshipOrderInterface::UPDATED_AT);
    }

    /**
     * Get the order if created or not on Dropship
     *
     * @return string
     */
    public function getOrderCreateDropship()
    {
        return $this->getData(DropshipOrderInterface::IS_DROPSHIP_CREATED);
    }

    /**
     * Set status if order is created on Dropship
     *
     * @param string $status
     * @return $this
     */
    public function setOrderCreateDropship($status)
    {
        return $this->setData(DropshipOrderInterface::IS_DROPSHIP_CREATED, $status);
    }

    /**
     * Set Sales Order Id
     *
     * @param string $orderId
     * @return $this
     */
    public function setSalesOrderId($orderId)
    {
        return $this->setData(DropshipOrderInterface::SALES_ORDER_ID, $orderId);
    }

    /**
     * Get Sales order Id
     *
     * @return string
     */
    public function getSalesOrderId()
    {
        return $this->getData(DropshipOrderInterface::SALES_ORDER_ID);
    }

    /**
     * Get Dropship Order Id
     *
     * @return string
     */
    public function getDropshipOrderId()
    {
        return $this->getData(DropshipOrderInterface::DROPSHIP_ORDER_ID);
    }

    /**
     * Set Dropship Order Id
     *
     * @param string $orderDropshipId
     * @return $this
     */
    public function setDropshipOrderId($orderDropshipId)
    {
        return $this->setData(DropshipOrderInterface::DROPSHIP_ORDER_ID, $orderDropshipId);
    }

    /**
     * Get the Invoice url
     *
     * @return string
     */
    public function getDropshipInvoiceUrl()
    {
        return $this->getData(DropshipOrderInterface::DROPSHIP_INVOICE_URL);
    }

    /**
     * Set the Dropship Invoice Url
     *
     * @param string $url
     * @return $this
     */
    public function setDropshipInvoiceUrl($url)
    {
        return $this->setData(DropshipOrderInterface::DROPSHIP_INVOICE_URL, $url);
    }
}
