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

namespace Ced\Dropship\Api;

use Ced\Dropship\Api\Data\DropshipOrderInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface DropshipOrderRepositoryInterface
{
    /**
     * Get Dropship Order by Id
     *
     * @param int $id
     * @return \Ced\Dropship\Api\Data\DropshipOrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Save Dropship Order object
     *
     * @param \Ced\Dropship\Api\Data\DropshipOrderInterface $dropshipOrder
     * @return \Ced\Dropship\Api\Data\DropshipOrderInterface
     */
    public function save(DropshipOrderInterface $dropshipOrder);

    /**
     * Delete the Dropship Order from database
     *
     * @param \Ced\Dropship\Api\Data\DropshipOrderInterface $dropshipOrder
     * @return void
     */
    public function delete(DropshipOrderInterface $dropshipOrder);

    /**
     * Get a list of Dropship Order objects
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ced\Dropship\Api\Data\DropshipOrderSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Dropship Order using the ID
     *
     * @param int $dropshipOrderId
     *
     * @return boolean
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($dropshipOrderId);

    /**
     * Get DropshipOrder via Incremental customer order id
     *
     * @param int $incrematalId
     * @return \Ced\Dropship\Api\Data\DropshipOrderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function loadByIncrementalId($incrematalId);
}
