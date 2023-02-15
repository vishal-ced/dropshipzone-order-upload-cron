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

namespace Ced\Dropship\Model\ResourceModel\DropshipOrder;

/**
 * Class Collection of order record of Dropship
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Standard resource collection initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(
            \Ced\Dropship\Model\DropshipOrder::class,
            \Ced\Dropship\Model\ResourceModel\DropshipOrder::class
        );
    }
}
