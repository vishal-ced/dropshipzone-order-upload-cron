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
use Ced\Dropship\Api\Data\DropshipOrderInterfaceFactory;
use Ced\Dropship\Api\DropshipOrderRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Ced\Dropship\Model\ResourceModel\DropshipOrder;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Ced\Dropship\Model\ResourceModel\DropshipOrder\CollectionFactory as DropshipOrderCollection;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Class DropshipOrderRepository dropship order Repository
 */
class DropshipOrderRepository implements DropshipOrderRepositoryInterface
{
    /**
     * @var DropshipOrderInterface
     */
    protected $dropshipOrder;

    /**
     * @var dropshipOrder
     */
    protected $dropshipOrderResource;

    /**
     * @var DropshipOrderCollection
     */
    protected $dropshipOrderCollection;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    // /**
    //  * @var SearchResultsFactory
    //  */
    // protected $searchResultsFactory;

    /**
     * @var dropshipOrderInterfaceFactory
     */
    protected $dropshipOrderInterFactory;

    /**
     * DropshipOrderRepository constructor.
     *
     * @param DropshipOrderInterface $dropshipOrder
     * @param DropshipOrderInterfaceFactory $dropshipOrderInterfaceFactory
     * @param DropshipOrder $dropshipOrderResource
     * @param DropshipOrderCollection $dropshipOrderCollection
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        DropshipOrderInterface $dropshipOrder,
        DropshipOrderInterfaceFactory $dropshipOrderInterfaceFactory,
        DropshipOrder $dropshipOrderResource,
        DropshipOrderCollection $dropshipOrderCollection,
        // SearchResultsFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->dropshipOrder = $dropshipOrder;
        $this->dropshipOrderInterFactory = $dropshipOrderInterfaceFactory;
        $this->dropshipOrderResource = $dropshipOrderResource;
        $this->dropshipOrderCollection = $dropshipOrderCollection;
        $this->collectionProcessor = $collectionProcessor;
        // $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * Get dropship Order by Id
     *
     * @param int $id
     * @return \Ced\Dropship\Api\Data\DropshipOrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $dropshipOrder = $this->dropshipOrderInterFactory->create();
        $this->dropshipOrderResource->load($dropshipOrder, $id);
        if (!$dropshipOrder->getId()) {
            throw new NotFoundException(__("Requested data does not exist."));
        }
        return $dropshipOrder;
    }

    /**
     * Save dropship Order object
     *
     * @param \Ced\Dropship\Api\Data\DropshipOrderInterface $dropshipOrder
     * @return \Ced\Dropship\Api\Data\DropshipOrderInterface
     */
    public function save(DropshipOrderInterface $dropshipOrder)
    {
        try {
            $this->dropshipOrderResource->save($dropshipOrder);
        } catch (Exception $exception) {
            // var_dump($exception->getMessage()); die;
            throw new CouldNotSaveException(__("Counld not save the Dropship Order"));
        }
        return $dropshipOrder;
    }

    /**
     * Delete the dropship Order from database
     *
     * @param \Ced\Dropship\Api\Data\DropshipOrderInterface $dropshipOrder
     * @return void
     */
    public function delete(DropshipOrderInterface $dropshipOrder)
    {
        try {
            $this->dropshipOrderResource->delete($dropshipOrder);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
    }

    /**
     * Get a list of dropship Order objects
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ced\Dropship\Api\Data\DropshipOrderSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        // $collection = $this->dropshipOrderCollection->create();
        // $this->collectionProcessor->process($searchCriteria, $collection);

        // $searchResults = $this->searchResultsFactory->create();
        // $searchResults->setSearchCriteria($searchCriteria);
        // $searchResults->setItems($collection->getItems());
        // $searchResults->setTotalCount($collection->getSize());
        // return $searchResults;
        return 1;
    }

    /**
     * Delete dropship Order using the ID
     *
     * @param int $dropshipOrderId
     * @return boolean
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($dropshipOrderId)
    {
        try {
            $this->delete($this->getById($dropshipOrderId));
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Get dropshipOrder via Incremental customer order id
     *
     * @param int $incrematalId
     * @return \Ced\Dropship\Api\Data\DropshipOrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function loadByIncrementalId($incrematalId)
    {
        $dropshipOrder = $this->dropshipOrderCollection->create()
                        ->addFieldToFilter("customer_order_reference", ["eq" => $incrematalId])->getFirstItem();
        if (!$dropshipOrder->getId()) {
            throw new NotFoundException(__("Requested data does not exist."));
        }
        return $dropshipOrder;
    }
}
