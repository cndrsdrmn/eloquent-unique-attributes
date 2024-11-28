<?php

namespace Workbench\App\Models;

use Cndrsdrmn\EloquentUniqueAttributes\HasEnforcesUniqueAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Workbench\Database\Factories\ProductFactory;

class Product extends Model
{
    use HasEnforcesUniqueAttributes;

    /** @use HasFactory<\Workbench\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string|null
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'barcode',
        'sku',
        'price',
    ];

    /**
     * Define the configuration for columns requiring unique enforcement.
     *
     * @return array<array-key, array<array-key, string|int>>
     */
    public function enforcedUniqueColumns(): array
    {
        return [];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
