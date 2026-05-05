<?php

namespace App\SaleLine\Domain\ValueObject;

final readonly class SaleLinePrice
{
    private int $value;

    private function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Sale line price must be greater than or equal to 0.');
        }

        $this->value = $value;
    }

    public static function create(int $value): self
    {
        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }
}
