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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Actions action defined
 */
class Actions extends Column
{
    /**
     * Sales order view
     */
    const URL_PATH_EDIT = 'sales/order/view';

    /**
     * Upload the order from Magento to dropship
     */
    const UPLOAD_ORDER = 'dropship/order/upload';

    /**
     * Get the status from dropship to Magento
     */
    const SYNC_ORDER = 'dropship/order/syncstatus';

    /**
     * Download Invoice URL
     */
    const INVOICE_DOWNLOAD = 'dropship/order/downloadinvoice';

    /**
     * @var UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Actions constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param  array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    if ($item['customer_order_reference']) {
                        $item[$name]['edit'] = [
                            'href' => $this->urlBuilder->getUrl(
                                self::URL_PATH_EDIT,
                                [
                                    'order_id' => $item['sales_order_id']
                                ]
                            ),
                            'label' => __('Edit'),
                            'class' => 'cedcommerce actions edit'
                        ];
                    }

                    if ($item["is_created_dropship"] == "No") {
                        $item[$name]['upload'] = [
                            'href' => $this->urlBuilder->getUrl(self::UPLOAD_ORDER, ['id' => $item['id']]),
                            'label' => __('Upload'),
                            'class' => 'cedcommerce actions upload'
                        ];
                    }

                    // $item[$name]['sync'] = [
                    //     'href' => $this->urlBuilder->getUrl(self::SYNC_ORDER, ['id' => $item['id']]),
                    //     'label' => __('Sync'),
                    //     'class' => 'cedcommerce actions download'
                    // ];

                    // if ($item["dropship_order_status"] == "Sent") {
                    //     if ($item["dropship_invoice_url"]) {
                    //         $item[$name]['download'] = [
                    //             'href' => $item["dropship_invoice_url"],
                    //             'label' => __('Download Invoice'),
                    //             'class' => 'cedcommerce actions download'
                    //         ];
                    //     } else {
                    //         $item[$name]['download'] = [
                    //             'href' => $this->urlBuilder->getUrl(self::INVOICE_DOWNLOAD, ['id' => $item['id']]),
                    //             'label' => __('Download Invoice'),
                    //             'class' => 'cedcommerce actions download'
                    //         ];
                    //     }
                    // }
                }
            }
        }
        return $dataSource;
    }
}
