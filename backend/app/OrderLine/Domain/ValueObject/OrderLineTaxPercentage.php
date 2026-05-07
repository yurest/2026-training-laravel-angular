<?php

namespace App\OrderLine\Domain\ValueObject;

final readonly class OrderLineTaxPercentage
{
    private int $value;

    private function __construct(int $value)
    {
        if ($value < 0 || $value > 100) {
            throw new \InvalidArgumentException('Order line tax percentage must be between 0 and 100.');
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