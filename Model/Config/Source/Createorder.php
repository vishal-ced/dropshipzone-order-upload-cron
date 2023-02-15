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

namespace Ced\Dropship\Model\Config\Source;

/**
 * Class Createorder to get order status when to create order
 */
class Createorder implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Order creation base
     *
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'ORDER_PLACED', 'label' => __('When Order Placed on Store')],
            ['value' => 'ORDER_MANUALLY', 'label' => __('Will Create Manually')],
        ];
    }
}
