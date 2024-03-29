<?php

namespace Dotdigitalgroup\EmailGraphQl\Model\Resolver;

use Dotdigitalgroup\Email\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class ProductBrands implements ResolverInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param Data $helper
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Data $helper,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        if (empty($args['product_ids'])) {
            throw new GraphQlInputException(__('Required parameter "product_id" is missing'));
        }

        $brands = [];
        $productIds = array_map('intval', $args['product_ids']);
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', $productIds, 'in')
            ->create();
        $products = $this->productRepository->getList($searchCriteria);
        $brandKey = $this->helper->getBrandAttributeByWebsiteId($this->storeManager->getStore()->getWebsiteId());

        foreach ($products->getItems() as $product) {
            $brand = $product->getCustomAttribute($brandKey);
            if ($brand) {
                $brands[] = [
                    "brand" => $brand->getValue(),
                    "product_id" => $product->getId()
                ];
            }
        }

        return ["items" => $brands];
    }
}
