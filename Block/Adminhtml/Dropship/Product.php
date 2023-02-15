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

namespace Ced\Dropship\Block\Adminhtml\Dropship;

/**
 * Class Product block to get functions in phtml
 */
class Product extends \Magento\Framework\View\Element\Template
{
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
     * Constructor function
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Ced\Dropship\Helper\Config $helper
     * @param \Ced\Dropship\Helper\Request $requestHelper
     * @param \Ced\Dropship\Helper\Product $productHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Ced\Dropship\Helper\Config $helper,
        \Ced\Dropship\Helper\Request $requestHelper,
        \Ced\Dropship\Helper\Product $productHelper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->requestHelper = $requestHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Check Api is valid or not
     *
     * @return bool
     */
    public function validateApi()
    {
        $user = $this->helper->getCustomerEmail();
        $pass = $this->helper->getCustomerPassword();
        $return = $this->requestHelper->getAuthToken($user, $pass);
        // $response = json_decode($return);
        if ($return == "Unauthorized") {
            return 0;
        } else {
            return 1;
        }
    }
}
