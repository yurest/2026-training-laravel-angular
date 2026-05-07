<?php

namespace App\Order\Domain\ValueObject;

use InvalidArgumentException;

final readonly class OrderStatus
{
    private const OPEN = 'open';
    private const CANCELLED = 'cancelled';
    private const INVOICED = 'invoiced';

    private const ALLOWED = [
        self::OPEN,
        self::CANCELLED,
        self::INVOICED,
    ];

    private function __construct(
        private string $value,
    ) {}

    public static function create(string $value): self
    {
        $value = strtolower(trim($value));

        if (!in_array($value, self::ALLOWED, true)) {
            throw new InvalidArgumentException('Invalid order status.');
        }

        return new self($value);
    }

    public static function open(): self
    {
        return new self(self::OPEN);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public static function invoiced(): self
    {
        return new self(self::INVOICED);
    }

    public function value(): string
    {
        return $this->value;
    }
}