<?php

namespace App\Restaurant\Domain\Exception;

final class CannotUpdateLegalDataException extends \DomainException
{
    public static function create(): self
    {
        return new self('Forbidden. Only superadmins can update legal data.');
    }
}
