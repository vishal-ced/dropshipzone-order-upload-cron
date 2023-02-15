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

namespace Ced\Dropship\Ui\Component\Listing\Order;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class DropshipStatus to get status
 */
class DropshipStatus extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->getLabel($item[$this->getData('name')]);
            }
        }
        return $dataSource;
    }

    /**
     * To get label
     *
     * @param string $statusId
     * @return string
     */
    private function getLabel($statusId)
    {
        $statusLabel = "";

        switch ($statusId) {
            case "1":
                $statusLabel = "Order Placed";
                break;
            case "-1":
                $statusLabel = "Error on Dropship";
                break;
            case "4":
                $statusLabel = "Being Prepared ";
                break;
            case "5":
                $statusLabel = "Sent";
                break;
            case "7":
                $statusLabel = "Cancelled";
                break;
            case "8":
                $statusLabel = "Refunded";
                break;
            case "9":
                $statusLabel = "Replaced";
                break;
            default:
                $statusLabel = "Not Uploaded";
        }

        return $statusLabel;
    }
}
