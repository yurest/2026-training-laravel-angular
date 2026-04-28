<?php

namespace App\Product\Domain\ValueObject;

final readonly class ProductPrice
{
    private int $value;

    private function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Product price cannot be negative.');
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
