<?php

use Cndrsdrmn\PhpStringFormatter\StringFormatter;
use Illuminate\Support\Facades\Event;
use Workbench\App\Models\Product;
use Workbench\App\Models\ProductWithCustomConfig;
use Workbench\App\Models\ProductWithDefaultConfig;
use Workbench\App\Models\ProductWithSoftDeletes;

afterEach(function (): void {
    StringFormatter::createLexifyNormally();
});

it('enforce unique attributes do not execute without defining columns on configuration', function (): void {
    Event::fake([
        'eloquent.enforcing: '.Product::class,
        'eloquent.enforced: '.Product::class,
    ]);

    $product = Product::query()->create([
        'barcode' => 'barcode',
        'sku' => 'sku',
        'name' => 'Product name',
        'price' => 100,
    ]);

    expect($product->barcode)
        ->toBe('barcode')
        ->and($product->sku)
        ->toBe('sku');

    Event::assertDispatched('eloquent.enforcing: '.Product::class);
    Event::assertDispatched(
        'eloquent.enforced: '.Product::class,
        fn ($event, $response): bool => $response[1] === false
    );
});

it('enforce unique attributes has been executed with default configuration columns', function (): void {
    Event::fake([
        'eloquent.enforcing: '.ProductWithDefaultConfig::class,
        'eloquent.enforced: '.ProductWithDefaultConfig::class,
    ]);

    $product = ProductWithDefaultConfig::query()->create([
        'name' => 'Product name',
        'price' => 100,
    ]);

    expect($product->name)
        ->toBe('Product name')
        ->and($product->price)
        ->toBe(100)
        ->and($product->barcode)
        ->toHaveLength(8)
        ->and($product->sku)
        ->toHaveLength(8);

    Event::assertDispatched('eloquent.enforcing: '.ProductWithDefaultConfig::class);
    Event::assertDispatched(
        'eloquent.enforced: '.ProductWithDefaultConfig::class,
        fn ($event, $response): bool => $response[1] === true
    );
});

it('enforce unique attributes has been executed with custom configuration columns', function (): void {
    ProductWithCustomConfig::withoutEvents(fn (): ProductWithCustomConfig => ProductWithCustomConfig::query()->create([
        'name' => 'Product unique',
        'price' => 75,
        'barcode' => 'barcode_unique',
        'sku' => 'SKU_unique',
    ]));

    Event::fake([
        'eloquent.enforcing: '.ProductWithCustomConfig::class,
        'eloquent.enforced: '.ProductWithCustomConfig::class,
    ]);

    StringFormatter::createLexifyUsing(fn (string $value): string => strlen($value) > 5 ? 'custom' : 'unique');

    $product = ProductWithCustomConfig::query()->create([
        'name' => 'Product name',
        'price' => 100,
    ]);

    expect($product->sku)
        ->toBe('SKU_custom');

    $this->assertDatabaseCount('products', 2);

    Event::assertDispatched('eloquent.enforcing: '.ProductWithCustomConfig::class);
    Event::assertDispatched(
        'eloquent.enforced: '.ProductWithCustomConfig::class,
        fn ($event, $response): bool => $response[1] === true
    );
});

it('enforce unique attributes has affected to model with soft deletes trait', function (): void {
    ProductWithSoftDeletes::withoutEvents(fn (): ?bool => ProductWithSoftDeletes::query()->create([
        'name' => 'Product unique',
        'price' => 75,
        'barcode' => 'barcode_unique',
        'sku' => 'SKU_unique',
    ])->delete());

    Event::fake([
        'eloquent.enforcing: '.ProductWithSoftDeletes::class,
        'eloquent.enforced: '.ProductWithSoftDeletes::class,
    ]);

    StringFormatter::createLexifyUsing(fn (string $value): string => strlen($value) > 5 ? 'custom' : 'unique');

    $product = ProductWithSoftDeletes::query()->create([
        'name' => 'Product name',
        'price' => 100,
    ]);

    expect($product->sku)
        ->toBe('SKU_custom');

    Event::assertDispatched('eloquent.enforcing: '.ProductWithSoftDeletes::class);
    Event::assertDispatched(
        'eloquent.enforced: '.ProductWithSoftDeletes::class,
        fn ($event, $response): bool => $response[1] === true
    );
});

it('override enforce unique using listener', function (): void {
    Event::fake([
        'eloquent.enforced: '.Product::class,
    ]);

    $this->app['events']->listen('eloquent.enforcing: '.Product::class, function (Product $model): bool {
        $model->setAttribute('barcode', 'barcode_unique');
        $model->setAttribute('sku', 'sku_unique');

        return true;
    });

    $product = Product::query()->create([
        'name' => 'Product name',
        'price' => 100,
    ]);

    expect($product->barcode)
        ->toBe('barcode_unique')
        ->and($product->sku)
        ->toBe('sku_unique');

    Event::assertNotDispatched('eloquent.enforced: '.Product::class);
});
