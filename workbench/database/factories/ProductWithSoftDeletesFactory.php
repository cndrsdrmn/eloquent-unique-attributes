<?php

namespace Workbench\Database\Factories;

use Workbench\App\Models\ProductWithSoftDeletes;

/**
 * @template TModel of \Workbench\App\Models\ProductWithSoftDeletes
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class ProductWithSoftDeletesFactory extends ProductFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = ProductWithSoftDeletes::class;
}
