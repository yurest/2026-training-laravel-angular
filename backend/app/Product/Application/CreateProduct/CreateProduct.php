<?php

namespace App\Product\Application\CreateProduct;

use App\Product\Domain\Entity\Product;
use App\Product\Domain\Interfaces\ProductRepositoryInterface;
use App\Product\Domain\ValueObject\ProductImageSrc;
use App\Product\Domain\ValueObject\ProductName;
use App\Product\Domain\ValueObject\ProductPrice;
use App\Product\Domain\ValueObject\ProductStock;
use App\Shared\Domain\ValueObject\FamilyId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\TaxId;

final class CreateProduct
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $familyId,
        string $taxId,
        int $stock,
        string $imageSrc,
        string $name,
        int $price
    ): CreateProductResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $familyIdVO = FamilyId::create($familyId);
        $taxIdVO = TaxId::create($taxId);
        $stockVO = ProductStock::create($stock);
        $imageSrcVO = ProductImageSrc::create($imageSrc);
        $nameVO = ProductName::create($name);
        $priceVO = ProductPrice::create($price);
        $product = Product::dddCreate(
            $restaurantIdVO,
            $familyIdVO,
            $taxIdVO,
            $stockVO,
            $imageSrcVO,
            $nameVO,
            $priceVO
        );
        $this->productRepository->save($product);

        return CreateProductResponse::create($product);
    }
}
