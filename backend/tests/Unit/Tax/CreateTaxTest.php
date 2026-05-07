<?php

namespace Tests\Unit\Tax;

use App\Tax\Application\CreateTax\CreateTax;
use App\Tax\Application\CreateTax\CreateTaxCommand;
use App\Tax\Application\CreateTax\CreateTaxResponse;
use App\Tax\Domain\Entity\Tax;
use App\Tax\Domain\Exception\TaxNameAlreadyExistsException;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateTaxTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_invoke_creates_tax_and_saves_it(): void
    {
        $repository = Mockery::mock(TaxRepositoryInterface::class);
        $repository->shouldReceive('existsByName')
            ->once()
            ->with('IVA Test')
            ->andReturn(false);

        $repository->shouldReceive('save')
            ->once()
            ->with(Mockery::on(function (Tax $tax): bool {
                return $tax->name()->value() === 'IVA Test' && $tax->percentage()->value() === 7;
            }));

        $createTax = new CreateTax($repository);
        $response = $createTax(new CreateTaxCommand('IVA Test', 7));

        $this->assertInstanceOf(CreateTaxResponse::class, $response);
        $this->assertSame('IVA Test', $response->name);
        $this->assertSame(7, $response->percentage);
    }

    public function test_invoke_throws_when_tax_name_already_exists(): void
    {
        $repository = Mockery::mock(TaxRepositoryInterface::class);
        $repository->shouldReceive('existsByName')
            ->once()
            ->with('IVA Test')
            ->andReturn(true);
        $repository->shouldNotReceive('save');

        $createTax = new CreateTax($repository);

        $this->expectException(TaxNameAlreadyExistsException::class);

        $createTax(new CreateTaxCommand('IVA Test', 7));
    }
}
