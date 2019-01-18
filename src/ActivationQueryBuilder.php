<?php

namespace IslemKms\Activable;



trait ActivationQueryBuilder
{
    /**
     * Get a new query builder that only includes pending resources.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function pending()
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->pending();
    }

    /**
     * Get a new query builder that only includes inactive resources.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function inactive()
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->inactive();
    }

    /**
     * Get a new query builder that only includes postponed resources.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function postponed()
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->postponed();
    }

    /**
     * Get a new query builder that includes pending resources.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function withPending()
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->withPending();
    }

    /**
     * Get a new query builder that includes inactive resources.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function withInactive()
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->withInactive();
    }

    /**
     * Get a new query builder that includes postponed resources.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function withPostponed()
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->withPostponed();
    }

    /**
     * Get a new query builder that includes all resources.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function withAnyStatus()
    {
        return (new static)->newQueryWithoutScope(new ActivationScope());
    }
}