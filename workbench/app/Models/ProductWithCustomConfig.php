<?php

namespace Workbench\App\Models;

class ProductWithCustomConfig extends Product
{
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
}
