<?php

namespace Dotdigitalgroup\EmailGraphQl\Model\Resolver;

use Dotdigitalgroup\Email\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;

class ProductBrand implements ResolverInterface
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
     * @param Data $helper
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helper,
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
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
        if (empty($args['product_id'])) {
            throw new GraphQlInputException(__('Required parameter "product_id" is missing'));
        }

        $productId = $args["product_id"];

        try {
            $product = $this->productRepository->getById($productId);
            $brand = $product->getCustomAttribute(
                $this->helper->getBrandAttributeByWebsiteId($this->storeManager->getStore()->getWebsiteId())
            );
        } catch (NoSuchEntityException $e) {
            $brand = null;
        }

        return [
            'value' => $brand->getValue()
        ];
    }
}
