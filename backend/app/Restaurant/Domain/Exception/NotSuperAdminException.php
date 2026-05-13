<?php

namespace App\Restaurant\Domain\Exception;

final class NotSuperAdminException extends \DomainException
{
    public static function create(): self
    {
        return new self('Only superadmins can create restaurants.');
    }
}
