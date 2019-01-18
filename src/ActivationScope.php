<?php

namespace IslemKms\Activable;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActivationScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = [
        'WithPending',
        'withInactive',
        'WithPostponed',
        'WithAnyStatus',
        'Pending',
        'Inactive',
        'Postponed',
        'Active',
        'Activate',
        'Deactivate',
        'Postpone',
        'Pend'
    ];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $strict = (isset($model::$strictActivation))
            ? $model::$strictActivation
            : config('activable.strict');

        if ($strict) {
            $builder->where($model->getQualifiedStatusColumn(), '=', Status::ACTIVE);
        } else {
            $builder->where($model->getQualifiedStatusColumn(), '!=', Status::INACTIVE);
        }

        $this->extend($builder);
    }

    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * (This method exists in order to achieve compatibility with laravel 5.1.*)
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function remove(Builder $builder, Model $model)
    {
        $builder->withoutGlobalScope($this);

        $column = $model->getQualifiedStatusColumn();
        $query = $builder->getQuery();

        $bindingKey = 0;

        foreach ((array)$query->wheres as $key => $where) {
            if ($this->isActivationConstraint($where, $column)) {
                $this->removeWhere($query, $key);

                // Here SoftDeletingScope simply removes the where
                // but since we use Basic where (not Null type)
                // we need to get rid of the binding as well
                $this->removeBinding($query, $bindingKey);
            }

            // Check if where is either NULL or NOT NULL type,
            // if that's the case, don't increment the key
            // since there is no binding for these types
            if (!in_array($where['type'], ['Null', 'NotNull'])) $bindingKey++;
        }

    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Add the with-pending extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addWithPending(Builder $builder)
    {
        $builder->macro('withPending', function (Builder $builder) {
            $this->remove($builder, $builder->getModel());

            return $builder->whereIN($this->getStatusColumn($builder), [Status::ACTIVE, Status::PENDING]);
        });
    }

    /**
     * Add the with-inactive extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addwithInactive(Builder $builder)
    {
        $builder->macro('withInactive', function (Builder $builder) {
            $this->remove($builder, $builder->getModel());

            return $builder->whereIN($this->getStatusColumn($builder),
                [Status::ACTIVE, Status::INACTIVE]);
        });
    }

    /**
     * Add the with-postpone extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addWithPostponed(Builder $builder)
    {
        $builder->macro('withPostponed', function (Builder $builder) {
            $this->remove($builder, $builder->getModel());

            return $builder->whereIN($this->getStatusColumn($builder),
                [Status::ACTIVE, Status::POSTPONED]);
        });
    }

    /**
     * Add the with-any-status extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addWithAnyStatus(Builder $builder)
    {
        $builder->macro('withAnyStatus', function (Builder $builder) {
            $this->remove($builder, $builder->getModel());
            return $builder;
        });
    }

    /**
     * Add the Active extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addActive(Builder $builder)
    {
        $builder->macro('active', function (Builder $builder) {
            $model = $builder->getModel();

            $this->remove($builder, $model);

            $builder->where($model->getQualifiedStatusColumn(), '=', Status::ACTIVE);

            return $builder;
        });
    }

    /**
     * Add the Pending extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addPending(Builder $builder)
    {
        $builder->macro('pending', function (Builder $builder) {
            $model = $builder->getModel();

            $this->remove($builder, $model);

            $builder->where($model->getQualifiedStatusColumn(), '=', Status::PENDING);

            return $builder;
        });
    }

    /**
     * Add the Inactive extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addInactive(Builder $builder)
    {
        $builder->macro('inactive', function (Builder $builder) {
            $model = $builder->getModel();

            $this->remove($builder, $model);

            $builder->where($model->getQualifiedStatusColumn(), '=', Status::INACTIVE);

            return $builder;
        });
    }

    /**
     * Add the Postponed extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addPostponed(Builder $builder)
    {
        $builder->macro('postponed', function (Builder $builder) {
            $model = $builder->getModel();

            $this->remove($builder, $model);

            $builder->where($model->getQualifiedStatusColumn(), '=', Status::POSTPONED);

            return $builder;
        });
    }

    /**
     * Add the Activate extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addActivate(Builder $builder)
    {
        $builder->macro('activate', function (Builder $builder, $id = null) {
            $builder->withAnyStatus();
            return $this->updateActivationStatus($builder, $id, Status::ACTIVE);
        });
    }

    /**
     * Add the Deactivate extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addDeactivate(Builder $builder)
    {
        $builder->macro('deactivate', function (Builder $builder, $id = null) {
            $builder->withAnyStatus();
            return $this->updateActivationStatus($builder, $id, Status::INACTIVE);

        });
    }

    /**
     * Add the Postpone extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addPostpone(Builder $builder)
    {
        $builder->macro('postpone', function (Builder $builder, $id = null) {
            $builder->withAnyStatus();
            return $this->updateActivationStatus($builder, $id, Status::POSTPONED);
        });
    }

    /**
     * Add the Postpone extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return void
     */
    protected function addPend(Builder $builder)
    {
        $builder->macro('pend', function (Builder $builder, $id = null) {
            $builder->withAnyStatus();
            return $this->updateActivationStatus($builder, $id, Status::PENDING);
        });
    }

    /**
     * Get the "deleted at" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     *
     * @return string
     */
    protected function getStatusColumn(Builder $builder)
    {
        if ($builder->getQuery()->joins && count($builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedStatusColumn();
        } else {
            return $builder->getModel()->getStatusColumn();
        }
    }

    /**
     * Remove scope constraint from the query.
     *
     * @param $query
     * @param  int $key
     *
     * @internal param \Illuminate\Database\Query\Builder $builder
     */
    protected function removeWhere($query, $key)
    {
        unset($query->wheres[$key]);

        $query->wheres = array_values($query->wheres);
    }

    /**
     * Remove scope constraint from the query.
     *
     * @param $query
     * @param  int $key
     *
     * @internal param \Illuminate\Database\Query\Builder $builder
     */
    protected function removeBinding($query, $key)
    {
        $bindings = $query->getRawBindings()['where'];

        unset($bindings[$key]);

        $query->setBindings($bindings);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param $id
     * @param $status
     *
     * @return bool|int
     */
    private function updateActivationStatus(Builder $builder, $id, $status)
    {

        //If $id parameter is passed then update the specified model
        if ($id) {
            $model = $builder->find($id);
            $model->{$model->getStatusColumn()} = $status;
            $model->{$model->getActivatedAtColumn()} = Carbon::now();
            //if activated_by in enabled then append it to the update
            if ($activated_by = $model->getActivatedByColumn()) {
                $model->{$activated_by} = \Auth::user()->getKey();
            }

            $model->save();
            return $model;
        }

        $update = [
            $builder->getModel()->getStatusColumn() => $status,
            $builder->getModel()->getActivatedAtColumn() => Carbon::now()
        ];
        //if activated_by in enabled then append it to the update
        if ($activated_by = $builder->getModel()->getActivatedByColumn()) {
            $update[$builder->getModel()->getActivatedByColumn()] = \Auth::user()->getKey();
        }
        return $builder->update($update);
    }

    /**
     * Determine if the given where clause is a activation constraint.
     *
     * @param  array $where
     * @param  string $column
     * @return bool
     */
    protected function isActivationConstraint(array $where, $column)
    {
        return $where['column'] == $column;
    }
}