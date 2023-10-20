<?php

namespace App\Service;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Entity\TaxRate;

/**
 * Handles payments for products.
 *
 **/
class PaymentService
{
    /**
     * Gets a total price for a product.
     *
     * @param TaxRate taxRate
     * @param Product product
     * @param Coupon|null coupon
     *
     * @return float the total price value
     *
     **/
    public function getTotalPrice(TaxRate $taxRate, Product $product, ?Coupon $coupon): float
    {
        $price = $product->getPrice();

        if ($coupon) {
            $price = $coupon->calculatePrice($price);
        }

        $totalPrice = $price + ($price / 100) * $taxRate->getValue();

        return $totalPrice;
    }

    /**
     * Processes a payment for a product.
     *
     * @param PaymentProviderEnum providerKey
     * @param TaxRate taxRate
     * @param Product product
     * @param Coupon|null coupon
     *
     *
     **/
    public function processPayment(PaymentProviderEnum $providerKey, TaxRate $taxRate, Product $product, ?Coupon $coupon): ?string
    {
        $totalPrice = $this->getTotalPrice($taxRate, $product, $coupon);

        return $this->pay($providerKey, $totalPrice);
    }

    /**
     * Make a payment.
     *
     * @param PaymentProviderEnum providerKey
     * @param float price
     *
     * @return bool|null indicates if the payment was successful or not
     *
     **/
    public function pay(PaymentProviderEnum $providerKey, float $price): ?bool
    {
        switch ($providerKey) {
            case PaymentProviderEnum::PAYPAL:
                $provider = new \PaypalPaymentProcessor();

                return $provider->pay($price);

            case PaymentProviderEnum::STRIPE:
                $provider = new \StripePaymentProcessor();

                return $provider->processPayment($price);
        }

        return null;
    }
}

enum PaymentProviderEnum: string
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';
}
