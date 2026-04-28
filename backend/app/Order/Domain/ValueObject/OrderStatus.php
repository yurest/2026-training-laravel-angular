<?php

namespace App\Order\Domain\ValueObject;

use InvalidArgumentException;

final readonly class OrderStatus
{
    private const OPEN = 'open';
    private const CLOSED = 'closed';
    private const CANCELLED = 'cancelled';

    private const ALLOWED = [
        self::OPEN,
        self::CLOSED,
        self::CANCELLED,
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

    public function value(): string
    {
        return $this->value;
    }
}