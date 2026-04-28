<?php

namespace App\Tax\Domain\ValueObject;

final readonly class TaxPercentage
{
    private int $value;

    private function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Tax percentage cannot be negative.');
        }

        if ($value > 100) {
            throw new \InvalidArgumentException('Tax percentage cannot be greater than 100.');
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
