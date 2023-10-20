<?php

namespace App\Request;

use App\Entity\Coupon;
use App\Entity\Product;
use App\Entity\TaxRate;
use App\Repository\TaxRateRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PaymentRequest extends Request
{
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Assert\Regex('/^\d+$/')]
    #[Assert\Type('string')]
    public $product;

    #[Assert\Regex('/^[A-Za-z]{2}[A-Za-z0-9]+$/')]
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Assert\Type('string')]
    public $taxNumber;

    #[Assert\Type('string')]
    public $couponCode;

    public ?TaxRate $taxRate;

    #[Assert\Type('string')]
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Assert\Choice(['paypal', 'stripe'])]
    public $paymentProcessor;

    public ?Coupon $couponEntity;

    public Product $productEntity;

    public function __construct(protected TaxRateRepository $taxRateRepository)
    {
    }

    /**
     * Validates the tax number.
     *
     **/
    #[Assert\Callback]
    public function validateTaxNumber(ExecutionContextInterface $context)
    {
        $countyCode = substr($this->taxNumber ?? '', 0, 2);

        $this->taxRate = $this->taxRateRepository->findOneBy([
            'countryCode' => $countyCode,
        ]);

        $pattern = $this->taxRate?->getCountryCode().$this->taxRate?->getPattern();

        preg_match("/^{$pattern}$/", $this->taxNumber, $matches);

        if (!$matches) {
            $context->buildViolation('Invalid tax number !')
                ->atPath('taxNumber')
                ->addViolation();
        }
    }
}
