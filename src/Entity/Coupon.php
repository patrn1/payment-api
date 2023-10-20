<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    public const DiscountTypes = CoupodDiscountTypesEnum::class;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column]
    private ?CoupodDiscountTypesEnum $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getType(): ?CoupodDiscountTypesEnum
    {
        return CoupodDiscountTypesEnum::tryFrom($this->type);
    }

    public function setType(CoupodDiscountTypesEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Calculates price according to the coupon.
     *
     * @param int $price
     *
     **/
    public function calculatePrice(float $price): ?float
    {
        switch ($this->type) {
            case CoupodDiscountTypesEnum::FIXED_DISCOUNT:
                $price = $price - $this->amount;
                break;

            case CoupodDiscountTypesEnum::PERCENT_DISCOUNT:
                $price = $price - ($price / 100) * $this->amount;
                break;
        }

        return max(0, $price);
    }
}

enum CoupodDiscountTypesEnum: int
{
    case FIXED_DISCOUNT = 0;
    case PERCENT_DISCOUNT = 1;
}
