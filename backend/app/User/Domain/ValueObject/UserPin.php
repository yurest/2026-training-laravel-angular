<?php

namespace App\User\Domain\ValueObject;

final readonly class UserPin
{
    private const LENGTH = 4;

    private string $value;

    private function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('User pin cannot be empty.');
        }

        if (!ctype_digit($trimmed)) {
            throw new \InvalidArgumentException('User pin must contain only digits.');
        }

        if (strlen($trimmed) !== self::LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('User pin must be exactly %d digits.', self::LENGTH),
            );
        }

        $this->value = $trimmed;
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}