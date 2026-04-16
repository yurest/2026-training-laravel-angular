<?php

namespace App\Restaurant\Domain\ValueObject;

final readonly class CompanyTaxId
{
    private const MAX_LENGTH = 255;

    private string $value;

    private function __construct(string $value)
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('Company tax id cannot be empty.');
        }

        if (mb_strlen($trimmed) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('Company tax id cannot exceed %d characters.', self::MAX_LENGTH),
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