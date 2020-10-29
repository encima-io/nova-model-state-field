<?php

namespace Encima\ModelState;

use Laravel\Nova\Fields\Select;
use Spatie\ModelStates\Validation\ValidStateRule;

class ModelState extends Select
{
    /**
     * Create a new model state field.
     *
     * @param  string  $name
     * @param  string|callable|null  $attribute
     * @param  callable|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->resolveUsing(
            function ($value, $resource, $attribute) {
                // We get the parent class name, since we want to validate the input
                $parent = (new \ReflectionClass($value))->getParentClass()->name;
                $this->rules('required', ValidStateRule::make($parent));

                // We get the current value and its possible transitions
                $this->options(function () use ($value) {
                    return collect($value->transitionableStates())
                        ->prepend($value)
                        ->mapWithKeys(function ($value) {
                            return [(string) $value => $value];
                        });
                });

                // We return the resolved value
                return $value;
            });

        $this->displayUsing(
            function ($value, $resource, $attribute) {
                return $value;
            });
    }
}
