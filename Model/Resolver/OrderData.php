<?php

namespace Dotdigitalgroup\EmailGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\Data\OrderInterface;

class OrderData implements ResolverInterface
{
    /**
     * @var OrderInterface
     */
    private $orderInterface;

    /**
     * OrderData constructor.
     * @param OrderInterface $orderInterface
     */
    public function __construct(
        OrderInterface $orderInterface
    ) {
        $this->orderInterface = $orderInterface;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (empty($args['order_id'])) {
            throw new GraphQlInputException(__('Required parameter "order_id" is missing'));
        }

        $orderId = $args['order_id'];

        $order = $this->orderInterface->loadByIncrementId($orderId);

        $items = [];

        foreach ($order->getItems() as $orderItem) {
            $items[] = $orderItem->getName();
        }
        return [
            'items' => array_unique($items),
            'total' => $order->getBaseGrandTotal()
        ];
    }
}
