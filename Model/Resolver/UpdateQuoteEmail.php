<?php

namespace Dotdigitalgroup\EmailGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Validator\EmailAddress as EmailValidator;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;
use Magento\Quote\Model\ResourceModel\Quote;

class UpdateQuoteEmail implements ResolverInterface
{
    /**
     * @var Quote
     */
    private $quoteResource;

    /**
     * @var EmailValidator
     */
    private $emailValidator;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var MaskedQuoteIdToQuoteIdInterface
     */
    private $maskedQuoteIdToQuoteId;

    /**
     * UpdateQuoteEmail constructor.
     * @param Quote $quoteResource
     * @param EmailValidator $emailValidator
     * @param CartRepositoryInterface $cartRepository
     * @param MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     */
    public function __construct(
        Quote $quoteResource,
        EmailValidator $emailValidator,
        CartRepositoryInterface $cartRepository,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
    ) {
        $this->quoteResource = $quoteResource;
        $this->emailValidator = $emailValidator;
        $this->cartRepository = $cartRepository;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $email = trim($args['email']);
        $cartHash = $args['cartId'];

        if (empty($email)) {
            throw new GraphQlInputException(
                __('You must supply an email address to update the quote table.')
            );
        }

        if (!$this->emailValidator->isValid($email)) {
            throw new GraphQlInputException(
                __('Invalid email address supplied.')
            );
        }

        try {
            $cartId = $this->maskedQuoteIdToQuoteId->execute($cartHash);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlNoSuchEntityException(
                __('Could not find a cart with ID "%masked_cart_id"', ['masked_cart_id' => $cartHash])
            );
        }

        try {
            /** @var \Magento\Quote\Model\Quote $cart */
            $cart = $this->cartRepository->get($cartId);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(
                __('Could not find a cart with ID "%masked_cart_id"', ['masked_cart_id' => $cartHash])
            );
        }

        if (!$cart->hasItems() || $cart->getCustomerEmail()) {
            return false;
        }

        try {
            $cart->setCustomerEmail($email);
            $this->quoteResource->save($cart);
        } catch (\Exception $e) {
            throw new GraphQlInputException(
                __((string)$e)
            );
        }

        return true;
    }
}
