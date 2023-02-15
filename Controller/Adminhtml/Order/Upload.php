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

namespace Ced\Dropship\Controller\Adminhtml\Order;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Ced\Dropship\Helper\Order;
use Ced\Dropship\Helper\Logger;
use Ced\Dropship\Api\DropshipOrderRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Upload the order on dropship
 */
class Upload extends \Magento\Backend\App\Action
{
    /**
     * @var Order
     */
    protected $orderHelper;

    /**
     * @var Logger
     */
    protected $loggerHelper;

    /**
     * @var DropshiplOrderRepositoryInterface
     */
    protected $dropshipOrderRepo;

    /**
     * Upload constructor.
     * @param Order $orderHelper
     * @param Logger $loggerHelper
     * @param DropshipOrderRepositoryInterface $dropshipOrderRepository
     * @param Context $context
     */
    public function __construct(
        Order $orderHelper,
        Logger $loggerHelper,
        DropshipOrderRepositoryInterface $dropshipOrderRepository,
        Context $context
    ) {
        $this->orderHelper = $orderHelper;
        $this->loggerHelper = $loggerHelper;
        $this->dropshipOrderRepo = $dropshipOrderRepository;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        try {
            $params = $this->getRequest()->getParams();
            if (isset($params["id"])) {
                $dropshipOrder = $this->dropshipOrderRepo->getById($params["id"]);
                $excution = $this->orderHelper->createOrderOnDropship($dropshipOrder);
                if ($excution == 2) {
                    // $this->messageManager->addErrorMessage(__("Order Id already exist in DropshipZone"));
                    return $this->_redirect('*/*/index');
                }
                elseif (!$excution) {
                    $this->messageManager->addErrorMessage(__("Something went wrong. Please check the Log."));
                    return $this->_redirect('*/*/index');
                }
            } else {
                $this->messageManager->addErrorMessage(__("Order Id is missing."));
                return $this->_redirect('*/*/index');
            }
            $this->messageManager->addSuccessMessage(__("Order has been successfully exported"));
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            $this->loggerHelper->error($exception->getMessage(), [
                'method' => __METHOD__
            ]);
        }
        return $this->_redirect('*/*/index');
    }
}
