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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index, Product import
 */
class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor function
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Load the page defined in view/adminhtml/layout
     *
     * @return Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $result */
        $this->_initAction()->_setActiveMenu(
            'Ced_Dropship::dropship'
        )->_addBreadcrumb(
            __('Dropship Zone Product Synchronization'),
            __('Dropship Zone Product Synchronization')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Dropship Zone Product Synchronization'));
        $this->_view->renderLayout();
    }

    /**
     * Set actionview
     *
     * @return $this
     */
    public function _initAction()
    {
        $this->_view->loadLayout();
        $this->_addBreadcrumb(__('Dropship Zone Product Synchronization'), __('Dropship Zone Product Synchronization'));
        $this->_addBreadcrumb(__('Dropship Zone Product Synchronization'), __('Dropship Zone Product Synchronization'));
        return $this;
    }
}
