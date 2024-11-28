<?php

namespace Cndrsdrmn\EloquentUniqueAttributes;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;

class EnforcesUniqueAttributesObserver
{
    /**
     * Create a new instance of the observer.
     */
    public function __construct(protected EnforceUniqueAttributes $service, protected Dispatcher $events)
    {
        //
    }

    /**
     * Handle the "creating" event for the model.
     */
    public function creating(Model $model): void
    {
        $this->enforceUniqueAttributes($model, 'creating');
    }

    /**
     * Enforce unique attributes on the model during the specified event.
     */
    protected function enforceUniqueAttributes(Model $model, string $event): bool
    {
        if ($this->fireEnforcingUniqueAttributes($model, $event)) {
            return false;
        }

        $enforced = $this->service->make($model);

        $this->fireEnforcedUniqueAttributes($model, $enforced);

        return $enforced;
    }

    /**
     * Dispatch the "enforced" event for the model.
     */
    protected function fireEnforcedUniqueAttributes(Model $model, bool $status): void
    {
        $this->events->dispatch('eloquent.enforced: '.$model::class, [$model, $status]);
    }

    /**
     * Trigger the "enforcing" event for the model and determine if the enforcement should proceed.
     */
    protected function fireEnforcingUniqueAttributes(Model $model, string $event): mixed
    {
        return $this->events->until('eloquent.enforcing: '.$model::class, [$model, $event]);
    }
}
