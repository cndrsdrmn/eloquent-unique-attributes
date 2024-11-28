<?php

namespace Workbench\App\Models;

class ProductWithDefaultConfig extends Product
{
    /**
     * Define the configuration for columns requiring unique enforcement.
     *
     * @return array<array-key, array<array-key, string|int>>
     */
    public function enforcedUniqueColumns(): array
    {
        return [
            'barcode',
            'sku',
        ];
    }
}
