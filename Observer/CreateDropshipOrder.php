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

namespace Ced\Dropship\Observer;

use Magento\Framework\Exception\LocalizedException;
use Ced\Dropship\Helper\Logger;

/**
 * Class CreateDropshipOrder to export order on dropship
 */
class CreateDropshipOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var /Ced/Dropship/Helper/Config
     */
    protected $helper;

    /**
     * @var /Ced/Dropship/Helper/Request
     */
    protected $requestHelper;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * @var /Ced/Dropship/Helper/Logger
     */
    protected $logger;

    /**
     * @var /Ced/Dropship/Helper/Order
     */
    protected $orderHelper;

    /**
     * CreateDropshipOrder constructor.
     *
     * @param Logger $logger
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Ced\Dropship\Helper\Request $requestHelper
     * @param \Ced\Dropship\Helper\Config $helper
     * @param \Ced\Dropship\Helper\Order $orderHelper
     */
    public function __construct(
        Logger $logger,
        \Magento\Backend\App\ConfigInterface $config,
        \Ced\Dropship\Helper\Request $requestHelper,
        \Ced\Dropship\Helper\Config $helper,
        \Ced\Dropship\Helper\Order $orderHelper
    ) {
        $this->config = $config;
        $this->orderHelper = $orderHelper;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->requestHelper = $requestHelper;
    }

    /**
     * Run execute function to export
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        //die('85 vishal singh');
        try {
            $order = $observer->getEvent()->getOrder();
            $items = $order->getAllVisibleItems();
            $productIds = [];
            foreach ($items as $item) {
                $productIds[]= $item->getProductId();
            }
            $orderType = [];
            $orderType = $this->orderHelper->checkIfDropshipOrder($productIds);
            if ($orderType) {
                $dropshipOrder = $this->orderHelper->createOrder($order);
                if ($this->helper->createOrderWhen() == "ORDER_PLACED") {
                    $excution = $this->orderHelper->createOrderOnDropship($dropshipOrder);
                    if (!$excution) {
                        throw new LocalizedException(__("Something went wrong. Please check the Log"));
                    }
                }
            }
        } catch (LocalizedException $exception) {
            if ($this->helper->getDebugMode()) {
                // $this->logger->warning($exception->getMessage(), [
                //     'method' => __METHOD__
                // ]);
                return false;
            }
            return false;
        } catch (Exception $e) {
            if ($this->helper->getDebugMode()) {
                $this->logger->error($e->getMessage(), [
                    'method' => __METHOD__
                ]);
            }
        }
    }
}
