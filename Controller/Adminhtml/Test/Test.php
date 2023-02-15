<?php
namespace Ced\Dropship\Controller\Adminhtml\Test;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Test for testing
 */
class Test extends Action
{
    /**
     * @var PageFactory $resultPageFactory
     */
    public $resultPageFactory;

    /**
     * @var Request $helper
     */
    public $helper;

    /**
     * @var Config $conHelper
     */
    public $conHelper;

    /**
     * Constructor function
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Ced\Dropship\Helper\Request $helper
     * @param \Ced\Dropship\Helper\Config $conHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Ced\Dropship\Helper\Request $helper,
        \Ced\Dropship\Helper\Config $conHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->conHelper = $conHelper;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $res = [];
        // Need to call it once
        $method = 'get';
        $url = "products?page_no=1&limit=40";
        $res = $this->helper->sendRequest($url, $method);
        $res = json_decode($res);
        if (property_exists($res, 'code')) {
            // echo $res->code;
        } else {
            // echo $this->conHelper->enableCron();
        }
    }
}
