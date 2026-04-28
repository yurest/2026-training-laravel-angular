<?php

namespace App\Order\Domain\ValueObject;

use InvalidArgumentException;

final readonly class Diners
{
    private function __construct(
        private int $value,
    ) {}

    public static function create(int $value): self
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Diners must be greater than 0.');
        }

        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }
}