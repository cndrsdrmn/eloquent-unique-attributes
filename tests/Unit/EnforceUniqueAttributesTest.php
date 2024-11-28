<?php

use Cndrsdrmn\EloquentUniqueAttributes\EnforceUniqueAttributes;
use Cndrsdrmn\PhpStringFormatter\StringFormatter;
use Workbench\App\Models\Product;
use Workbench\App\Models\ProductWithCustomConfig;
use Workbench\App\Models\ProductWithDefaultConfig;
use Workbench\App\Models\ProductWithSoftDeletes;

beforeEach(function (): void {
    $this->service = new EnforceUniqueAttributes;
});

afterEach(function (): void {
    StringFormatter::createLexifyNormally();
    Mockery::close();
});

it('enforce unique attributes do not execute without defining columns on configuration', function (): void {
    $value = $this->service->make($product = new Product);

    expect($value)->toBeFalse()
        ->and($product->getAttributes())
        ->toBeEmpty()
        ->and($product->hasAttribute('barcode'))
        ->toBeFalse()
        ->and($product->hasAttribute('sku'))
        ->toBeFalse();
});

it('enforce unique attributes has been executed with default configuration columns', function (): void {
    $value = $this->service->make($product = new ProductWithDefaultConfig);

    expect($value)->toBeTrue()
        ->and($product->getAttributes())
        ->not->toBeEmpty()
        ->and($product->hasAttribute('barcode'))
        ->toBeTrue()
        ->and($product->getAttribute('barcode'))
        ->toBeString()
        ->toHaveLength(8)
        ->and($product->hasAttribute('sku'))
        ->toBeTrue()
        ->and($product->getAttribute('sku'))
        ->toBeString()
        ->toHaveLength(8);
});

it('enforce unique attributes has been executed with custom configuration columns', function (): void {
    ProductWithCustomConfig::factory()->create([
        'barcode' => 'barcode_unique',
        'sku' => 'SKU_unique',
    ]);

    StringFormatter::createLexifyUsing(fn (string $value): string => strlen($value) > 5 ? 'custom' : 'unique');

    $value = $this->service->make($product = new ProductWithCustomConfig);

    expect($value)->toBeTrue()
        ->and($product->getAttributes())
        ->not->toBeEmpty()
        ->and($product->hasAttribute('barcode'))
        ->toBeTrue()
        ->and($product->getAttribute('barcode'))
        ->toBeString()
        ->toHaveLength(5)
        ->and($product->hasAttribute('sku'))
        ->toBeTrue()
        ->and($product->getAttribute('sku'))
        ->toBeString()
        ->toHaveLength(10)
        ->toMatch('/SKU_[a-z]{6}/');
});

it('enforce unique attributes has affected to model with soft deletes trait', function (): void {
    ProductWithSoftDeletes::factory()->trashed()->create([
        'barcode' => 'barcode_unique',
        'sku' => 'SKU_unique',
    ])->delete();

    StringFormatter::createLexifyUsing(fn (string $value): string => strlen($value) > 5 ? 'custom' : 'unique');

    $value = $this->service->make($product = new ProductWithSoftDeletes);

    expect($value)->toBeTrue()
        ->and($product->getAttributes())
        ->not->toBeEmpty()
        ->and($product->hasAttribute('barcode'))
        ->toBeTrue()
        ->and($product->getAttribute('barcode'))
        ->toBeString()
        ->toHaveLength(5)
        ->and($product->hasAttribute('sku'))
        ->toBeTrue()
        ->and($product->getAttribute('sku'))
        ->toBeString()
        ->toHaveLength(10)
        ->toMatch('/SKU_[a-z]{6}/');
});
