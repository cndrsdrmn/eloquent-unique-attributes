<?php

namespace Cndrsdrmn\EloquentUniqueAttributes;

use Cndrsdrmn\PhpStringFormatter\StringFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class EnforceUniqueAttributes
{
    /**
     * The model instance on which unique attributes are enforced.
     */
    protected Model $model;

    /**
     * Enforce unique attributes on the given model.
     */
    public function make(Model $model): bool
    {
        $this->model = $model;

        $attributes = collect($this->model->enforcedUniqueColumns())
            ->map(function ($config, $attribute): ?string {
                if (is_string($config) && is_numeric($attribute)) {
                    $attribute = $config;
                    $config = [];
                }

                return $this->applyUniqueValue($attribute, $config);
            })
            ->values();

        return $attributes->isNotEmpty() && $this->model->isDirty($attributes->all());
    }

    /**
     * Apply a unique value to a given attribute.
     *
     * @template TKey of array-key
     * @template TValue of array-key
     *
     * @param  array<TKey, TValue>  $config
     */
    protected function applyUniqueValue(string $attribute, array $config): ?string
    {
        $value = $this->buildUniqueValue($attribute, $config);

        $this->model->setAttribute($attribute, $value);

        return $attribute;
    }

    /**
     * Apply formatting (prefix, suffix, separator) to a value.
     *
     * @template TKey of array-key
     * @template TValue of array-key
     *
     * @param  array<TKey, TValue>  $config
     */
    protected function applyValueFormatter(string $value, array $config): string
    {
        $separator = $config['separator'];

        $formatter = implode($separator, [
            $config['prefix'],
            $value,
            $config['suffix'],
        ]);

        return trim($formatter, $separator);
    }

    /**
     * Build a format string based on the configuration.
     */
    protected function buildFormat(string $format, int $length): string
    {
        $symbol = match ($format) {
            'numerify' => '#',
            'lexify' => '?',
            default => '*',
        };

        return str_repeat($symbol, $length);
    }

    /**
     * Build a unique value for a given attribute.
     *
     * @template TKey of array-key
     * @template TValue of array-key
     *
     * @param  array<TKey, TValue>  $config
     */
    protected function buildUniqueValue(string $attribute, array $config): string
    {
        $config = $this->mergeConfiguration($config);

        return $this->generateUniqueValue($config, $this->getExistingValues($attribute));
    }

    /**
     * Generate a unique value based on configuration and existing resources.
     *
     * @template TKey of array-key
     *
     * @param  array<TKey, (array-key|mixed)>  $config
     * @param  Collection<int, string>  $resources
     */
    protected function generateUniqueValue(array $config, Collection $resources): string
    {
        $length = $config['length'];
        $attempts = 0;

        do {
            $format = $this->buildFormat($config['format'], $length);

            $value = match ($config['format']) {
                'numerify' => StringFormatter::numerify($format),
                'lexify' => StringFormatter::lexify($format),
                default => StringFormatter::bothify($format),
            };

            $formattedValue = $this->applyValueFormatter($value, $config);

            $attempts++;

            if ($attempts >= $config['max_retries']) {
                $length++;
                $attempts = 0;
            }
        } while ($resources->contains($formattedValue));

        return $formattedValue;
    }

    /**
     * Retrieve existing values for the specified attribute.
     *
     * @return Collection<int, string>
     */
    protected function getExistingValues(string $attribute): Collection
    {
        return $this->model->newQuery()
            ->when($this->useSoftDeletes(), fn ($query) => $query->withTrashed())
            ->pluck($attribute)
            ->toBase();
    }

    /**
     * Merge default configuration with overrides.
     *
     * @template TKey of array-key
     * @template TValue of array-key
     *
     * @param  array<TKey, TValue>  $overrides
     */
    protected function mergeConfiguration(array $overrides = []): array
    {
        $config = config()->array('unique_attributes', []);

        return array_merge($config, $overrides);
    }

    /**
     * Determine if the model uses soft deletes.
     */
    protected function useSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->model));
    }
}
