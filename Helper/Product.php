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
namespace Ced\Dropship\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\LocalizedException;


/**
 * Class Product, for api integration
 */
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Model\ProductFactory $productFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Ced\Dropship\Helper\Config
     */
    protected $_helper;

    /**
     * @var \Ced\Dropship\Helper\Request
     */
    protected $requestHelper;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $fileDriver;

    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory
     */
    protected $optionLabelFactory;

    /**
     * @var \Ced\Dropship\Helper\Logger
     */
    protected $logger;

    /**
     * @var File
     */
    protected $_file;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $fileSystemIo;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\TableFactory
     */
    protected $tableFactory;
    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory
     */
    protected $optionFactory;

    /**
     * Data Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Backend\App\ConfigInterface $config
     * @param \Ced\Dropship\Helper\Config $helper
     * @param \Ced\Dropship\Helper\Request $requestHelper
     * @param \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement
     * @param \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Ced\Dropship\Helper\Logger $logger
     * @param File $file
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory
     * @param \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory
     * @param \Magento\Framework\Filesystem\Io\File $fileSystemIo
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\App\ConfigInterface $config,
        \Ced\Dropship\Helper\Config $helper,
        \Ced\Dropship\Helper\Request $requestHelper,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Ced\Dropship\Helper\Logger $logger,
        File $file,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Framework\Filesystem\Io\File $fileSystemIo
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->requestHelper = $requestHelper;
        $this->config = $config;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->optionFactory = $optionFactory;
        $this->logger = $logger;
        $this->_file = $file;
        $this->fileDriver = $fileDriver;
        $this->filesystem = $filesystem;
        $this->tableFactory = $tableFactory;
        $this->attributeRepository = $attributeRepository;
        $this->fileSystemIo = $fileSystemIo;
    }

    /**
     * Create or get Category Id
     *
     * @param string $cat
     * @return categoryId
     */
    private function validateCategory($cat)
    {
        try{
            $categoryHeirarchy = explode(" > ", $cat);
            $parentId = 2;
            $id=0;
            foreach ($categoryHeirarchy as $categoryName) {
                $parentCategory = $this->categoryFactory->create()->load($parentId);
                $category = $this->categoryFactory->create();
                $cate = $category->getCollection()->addAttributeToFilter('name', $categoryName)->getFirstItem();
                if ($cate->getId() == '') {
                    $category->setPath($parentCategory->getPath())
                    ->setParentId($parentId)
                    ->setName($categoryName)
                    ->setIsActive(true);
                    $category->save();
                    $parentId = $category->getId();
                    $id = $category->getId();
                } else {
                    $id = $cate->getId();
                    $parentId = $cate->getId();
                }
            }
            return $id;
        } catch (Exception $e) {
            if ($this->_helper->getDebugMode()) {
                $this->logger->error($e->getMessage());
                return "Some error Occured.";
            }
            return "Some Error Occured, please check log.";
        }
        
    }

    /**
     * Create product
     *
     * @param string $productData
     * @var $productData
     */
    public function createProduct($productData)
    {
        // echo "<pre>";
        // var_dump($productData);
        // die;
        
        $product = $this->productFactory->create();

        // Product Status
        if ($this->_helper->createProductInDisableState() == 0) {
            $productStatus = 1;
        } else {
            $productStatus = 2;
        }

        // SKU Prefix
        $skuPrefix = $this->_helper->skuPrefix();
        $sku = $skuPrefix.$productData->sku;
        
        try {
            if ($product->getIdBySku($sku)) {
                // load product
                $product->load($product->getIdBySku($sku));

                // if stock 0 then disable product
                if ($this->_helper->createOutOfStockProducts() == 0) {
                    if ((int)$productData->stock_qty <= 0) {
                        $product->setStatus(2);
                        if ($product->save()) {
                            return "Product disabled";
                        } else {
                            return "Some error Occured.";
                        }
                    }
                }

                $finalPrice = $productData->price;
                if ($finalPrice == '') {
                    $finalPrice = 0;
                }
                if ($this->_helper->incPriceInPercent() > 0) {
                    $inc = $this->_helper->incPriceInPercent();
                    $finalPrice = $finalPrice + ($finalPrice*$inc/100);
                } elseif ($this->_helper->incFixedPrice() > 0) {
                    $inc = $this->_helper->incFixedPrice();
                    $finalPrice = $finalPrice + $inc;
                }
                $name = $productData->title;
                // Now update products
                $product->setName($name);
                $product->setWebsiteIds([1]);
                if ($this->_helper->getCreateCategory()) {
                    $catId = $this->validateCategory($productData->Category);
                    $product->setCategoryIds($catId);
                }

                $product->setAttributeSetId(4);
                $product->setStatus($productStatus);
                $product->setVisibility(4);
                $product->setTypeId('simple');
                $product->setPrice($finalPrice);

                if ($productData->colour != null) {
                    $colorId = $this->createOrGetId('color', $productData->colour);
                    $product->setColor($colorId);
                }

                $product->setIsDropship(1);
                $product->setWeight($productData->weight);
                $product->setDescription($productData->desc);

                $product->setStockData(
                    [
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => $productData->in_stock,
                        'qty' => $productData->stock_qty
                    ]
                );

                // Added image
                $filePath = $this->_helper->getMediaPath()."/catalog/product/";
                if (!$this->fileDriver->isDirectory($filePath)) {
                    $ioAdapter = $this->_file;
                    $ioAdapter->mkdir($filePath, 0777);
                    // $ioAdapter->mkdir($filePath);
                }

                $imgUrls = [];
                $imgUrls = $productData->gallery;
                if (count($imgUrls) > 0) {
                    $i = 1;
                    foreach ($imgUrls as $imgUrl) {
                        $mediaDirectory = $filePath . $productData->sku . '_' . 'Image_'.$i.'.jpg';
                        if ($this->getHttpResponseCode($imgUrl) == "200") {
                            $this->fileDriver->filePutContents(
                                $mediaDirectory,
                                $this->fileDriver->fileGetContents(trim($imgUrl))
                            );
                            if ($i == 1) {
                                $product->addImageToMediaGallery(
                                    $mediaDirectory,
                                    ['image', 'swatch_image', 'small_image', 'thumbnail'],
                                    false,
                                    false
                                );
                            } else {
                                $product->addImageToMediaGallery(
                                    $mediaDirectory,
                                    null,
                                    false,
                                    false
                                );
                            }
                            
                        }
                        $i++;
                    }
                }
                
                if ($this->_helper->createOutOfStockProducts() == 0) {
                    if ((int)$productData->stock_qty <= 0) {
                        return "Can't create product, product Out of stock.";
                    } else {
                        if ($product->save()) {
                            return "Product Created successfully.";
                        } else {
                            return "Some error Occured.";
                        }
                    }
                } else {
                    if ($product->save()) {
                        return "Product Created successfully.";
                    } else {
                        return "Some error Occured.";
                    }
                }
            } else {
                // Find Price
                $finalPrice = $productData->price;
                if ($finalPrice == '') {
                    $finalPrice = 0;
                }
                if ($this->_helper->incPriceInPercent() > 0) {
                    $inc = $this->_helper->incPriceInPercent();
                    $finalPrice = $finalPrice + ($finalPrice*$inc/100);
                } elseif ($this->_helper->incFixedPrice() > 0) {
                    $inc = $this->_helper->incFixedPrice();
                    $finalPrice = $finalPrice + $inc;
                }
                // echo $finalPrice."<br>";
                
                $name = $productData->title;
                
                // URL key of product
                $urlkey = preg_replace('#[^0-9a-z]+#i', '-', $name).'-'.preg_replace('#[^0-9a-z]+#i', '-', $sku);
                $url = strtolower($urlkey);

                // Now create products
                $product->setName($name);
                $product->setSku($sku);
                $product->setWebsiteIds([1]);
                $product->setUrlKey($url);
                if ($this->_helper->getCreateCategory()) {
                    $catId = $this->validateCategory($productData->Category);
                    $product->setCategoryIds($catId);
                }

                $product->setAttributeSetId(4);
                $product->setStatus($productStatus);
                $product->setVisibility(4);
                $product->setTypeId('simple');
                $product->setPrice($finalPrice);

                if ($productData->colour != '') {
                    if ($productData->colour != null && $productData->colour != '0') {
                        $colorId = $this->createOrGetId('color', $productData->colour);
                        $product->setColor($colorId);
                    }
                }

                $product->setIsDropship(1);
                $product->setWeight($productData->weight);
                $product->setDescription($productData->desc);

                $product->setStockData(
                    [
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => $productData->in_stock,
                        'qty' => $productData->stock_qty
                    ]
                );

                // Added image
                $filePath = $this->_helper->getMediaPath()."/catalog/product/";
                if (!$this->fileDriver->isDirectory($filePath)) {
                    $ioAdapter = $this->_file;
                    $ioAdapter->mkdir($filePath, 0777);
                }

                $imgUrls = [];
                $imgUrls = $productData->gallery;
                if (count($imgUrls) > 0) {
                    $i = 1;
                    foreach ($imgUrls as $imgUrl) {
                        $mediaDirectory = $filePath . $productData->sku . '_' . 'Image_'.$i.'.jpg';
                        if ($this->getHttpResponseCode($imgUrl) == "200") {
                            $this->fileDriver->filePutContents(
                                $mediaDirectory,
                                $this->fileDriver->fileGetContents(trim($imgUrl))
                            );
                            if ($i == 1) {
                                $product->addImageToMediaGallery(
                                    $mediaDirectory,
                                    ['image', 'swatch_image', 'small_image', 'thumbnail'],
                                    false,
                                    false
                                );
                            } else {
                                $product->addImageToMediaGallery(
                                    $mediaDirectory,
                                    null,
                                    false,
                                    false
                                );
                            }
                            
                        }
                        $i++;
                    }
                }
                
                // Remain to add category
                // Save Product
                if ($this->_helper->createOutOfStockProducts() == 0) {
                    if ((int)$productData->stock_qty <= 0) {
                        return "Can't create product, product Out of stock.";
                    } else {
                        if ($product->save()) {
                            return "Product Created successfully.";
                        } else {
                            return "Some error Occured.";
                        }
                    }
                } else {
                    if ($product->save()) {
                        return "Product Created successfully.";
                    } else {
                        return "Some error Occured.";
                    }
                }
            }

        } catch (Exception $e) {
            if ($this->_helper->getDebugMode()) {
                $this->logger->error($exception->getMessage());
                return "Some error Occured.";
            }
            return "Some error Occured.";
        }
    }

    /**
     * Create or get Attribute Id
     *
     * @param string $attributeCode
     * @param string $label
     * @return $id
     */
    public function createOrGetId($attributeCode, $label)
    {
        try{
            if (strlen($label) < 1) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Label for %1 must not be empty.', $attributeCode)
                );
            }
    
            // Does it already exist?
            $optionId = $this->getOptionIdByLabel($attributeCode, $label);
    
            if ($optionId == false || $optionId == '') {
                // If no, add it.
    
                /** @var \Magento\Eav\Model\Entity\Attribute\OptionLabel $optionLabel */
                $optionLabel = $this->optionLabelFactory->create();
                $optionLabel->setStoreId(0);
                $optionLabel->setLabel($label);
    
                $option = $this->optionFactory->create();
                $option->setLabel($label);
                $option->setStoreLabels([$optionLabel]);
                $option->setSortOrder(0);
                $option->setIsDefault(false);
    
                $this->attributeOptionManagement->add(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $this->getAttribute($attributeCode)->getAttributeId(),
                    $option
                );
    
                // Get the inserted ID. Should be returned from the installer, but it isn't.
                $optionId = $this->getOptionId($attributeCode, $label, true);
            }
    
            return $optionId;
        } catch (\LocalizedException $e) {
            if ($this->_helper->getDebugMode()) {
                $this->logger->error($e->getMessage());
                return "Some error Occured.";
            }
            return "Some Error Occured, please check log.";
        } catch (Exception $e) {
            if ($this->_helper->getDebugMode()) {
                $this->logger->error($e->getMessage());
                return "Some error Occured.";
            }
            return "Some Error Occured, please check log.";
        }
        
    }

    /**
     * Check if image file is there or not
     *
     * @param string $url
     * @return false|string
     */
    public function getHttpResponseCode($url)
    {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    /**
     * Get option Id
     *
     * @param string $attributeCode
     * @param string $label
     * @param bool $force
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOptionId($attributeCode, $label, $force = false)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->getAttribute($attributeCode);

        // Build option array if necessary
        if ($force === true || !isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];

            // We have to generate a new sourceModel instance each time through to prevent it from
            // referencing its _options cache. No other way to get it to pick up newly-added values.

            /** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }

        // Return false if does not exist
        return false;
    }

    /**
     * Get option id by label
     *
     * @param string $attributeCode
     * @param string $optionLabel
     * @return string
     */
    public function getOptionIdByLabel($attributeCode,$optionLabel)
    {
        $product = $this->productFactory->create();
        $isAttributeExist = $product->getResource()->getAttribute($attributeCode);
        $optionId = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionId = $isAttributeExist->getSource()->getOptionId($optionLabel);
        }
        return $optionId;
    }

    /**
     * Get attribute
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAttribute($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode);
    }
}
