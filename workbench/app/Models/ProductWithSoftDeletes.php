<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Workbench\Database\Factories\ProductWithSoftDeletesFactory;

class ProductWithSoftDeletes extends Product
{
    use SoftDeletes;

    /**
     * Define the configuration for columns requiring unique enforcement.
     *
     * @return array<array-key, array<array-key, string|int>>
     */
    public function enforcedUniqueColumns(): array
    {
        return [
            'barcode' => [
                'format' => 'numerify',
                'length' => 5,
            ],
            'sku' => [
                'format' => 'lexify',
                'length' => 5,
                'prefix' => 'SKU',
                'max_retries' => 2,
            ],
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ProductWithSoftDeletesFactory
    {
        return ProductWithSoftDeletesFactory::new();
    }
}
