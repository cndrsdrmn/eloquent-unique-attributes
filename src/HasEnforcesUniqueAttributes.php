<?php

namespace Cndrsdrmn\EloquentUniqueAttributes;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasEnforcesUniqueAttributes
{
    /**
     * Boot the trait and register the observer to enforce unique attributes.
     */
    protected static function bootHasEnforcesUniqueAttributes(): void
    {
        static::observe(EnforcesUniqueAttributesObserver::class);
    }

    /**
     * Define the configuration for columns requiring unique enforcement.
     *
     * Example configuration:
     * <code>
     * return [
     *     'column_name' => [
     *         'prefix' => 'PRE',
     *         'suffix' => 'SFX',
     *         'separator' => '-',
     *         'max_retries' => 100,
     *         'length' => 8,
     *         'format' => 'bothify', // Options: 'numerify', 'lexify', 'bothify'
     *     ],
     * ];
     * </code>
     */
    abstract public function enforcedUniqueColumns(): array;
}
