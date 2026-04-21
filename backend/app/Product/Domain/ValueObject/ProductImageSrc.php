<?php

namespace App\Product\Domain\ValueObject;

final readonly class ProductImageSrc
{
    private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    private function __construct(
        private ?string $value
    ) {}

    public static function create(?string $imageSrc): self
    {
        if ($imageSrc !== null) {
            $imageSrc = trim($imageSrc);

            if ($imageSrc === '') {
                $imageSrc = null;
            }
        }

        if ($imageSrc !== null && ! self::isValid($imageSrc)) {
            throw new \InvalidArgumentException('Invalid image source format.');
        }

        return new self($imageSrc);
    }

    private static function isValid(string $imageSrc): bool
    {
        if (! self::isValidUrl($imageSrc) && ! self::isValidLocalPath($imageSrc)) {
            return false;
        }

        return self::hasAllowedExtension($imageSrc);
    }

    private static function isValidUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    private static function isValidLocalPath(string $path): bool
    {
        return str_starts_with($path, '/') && strlen($path) > 1;
    }

    private static function hasAllowedExtension(string $imageSrc): bool
    {
        $path = parse_url($imageSrc, PHP_URL_PATH) ?: $imageSrc;
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }

    public function value(): ?string
    {
        return $this->value;
    }
}
