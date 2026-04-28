<?php

namespace App\User\Domain\ValueObject;

final readonly class UserRole
{
    private const ALLOWED_VALUES = [
        'admin',
        'operator',
    ];

    private string $value;

    private function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('User role cannot be empty.');
        }

        if (! in_array($trimmed, self::ALLOWED_VALUES, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'User role must be one of: %s.',
                    implode(', ', self::ALLOWED_VALUES),
                )
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
