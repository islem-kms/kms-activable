<?php

namespace IslemKms\Activable;



trait Activable
{
    use ActivationQueryBuilder;

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootActivable()
    {
        static::addGlobalScope(new ActivationScope);
    }

    /**
     * Change resource status to Active
     *
     * @param $id
     *
     * @return mixed
     */
    public static function activate($id)
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->activate($id);
    }

    /**
     * Change resource status to Inactive
     *
     * @param null $id
     *
     * @return mixed
     */
    public static function deactivate($id)
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->deactivate($id);
    }

    /**
     * Change resource status to Postpone
     *
     * @param null $id
     *
     * @return mixed
     */
    public static function postpone($id)
    {
        return (new static)->newQueryWithoutScope(new ActivationScope())->postpone($id);
    }

    /**
     * Change Instance's status to Active
     *
     * @return mixed
     */
    public function markActive()
    {
        $new = (new static)->newQueryWithoutScope(new ActivationScope())->activate($this->id);
        return $this->setRawAttributes($new->attributesToArray());
    }

    /**
     * Change Instance's status to Inactive
     *
     * @return mixed
     */
    public function markInactive()
    {
        $new = (new static)->newQueryWithoutScope(new ActivationScope())->deactivate($this->id);
        return $this->setRawAttributes($new->attributesToArray());
    }

    /**
     * Change Instance's status to Postponed
     *
     * @return mixed
     */
    public function markPostponed()
    {
        $new = (new static)->newQueryWithoutScope(new ActivationScope())->postpone($this->id);
        return $this->setRawAttributes($new->attributesToArray());
    }

    /**
     * Change Instance's status to Pending
     *
     * @return mixed
     */
    public function markPending()
    {
        $new = (new static)->newQueryWithoutScope(new ActivationScope())->pend($this->id);
        return $this->setRawAttributes($new->attributesToArray());
    }

    /**
     * Determine if the model instance has been activated.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->{$this->getStatusColumn()} == Status::ACTIVE;
    }

    /**
     * Determine if the model instance has been deactivated.
     *
     * @return bool
     */
    public function isInactive()
    {
        return $this->{$this->getStatusColumn()} == Status::INACTIVE;
    }

    /**
     * Determine if the model instance has been postponed.
     *
     * @return bool
     */
    public function isPostponed()
    {
        return $this->{$this->getStatusColumn()} == Status::POSTPONED;
    }

    /**
     * Determine if the model instance has been active.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->{$this->getStatusColumn()} == Status::PENDING;
    }

    /**
     * Get the name of the "status" column.
     *
     * @return string
     */
    public function getStatusColumn()
    {
        return defined('static::ACTIVATION_STATUS') ? static::ACTIVATION_STATUS : config('activable.status_column');
    }

    /**
     * Get the fully qualified "status" column.
     *
     * @return string
     */
    public function getQualifiedStatusColumn()
    {
        return $this->getTable() . '.' . $this->getStatusColumn();
    }

    /**
     * Get the fully qualified "activated at" column.
     *
     * @return string
     */
    public function getQualifiedActivatedAtColumn()
    {
        return $this->getTable() . '.' . $this->getActivatedAtColumn();
    }

    /**
     * Get the fully qualified "activated by" column.
     *
     * @return string
     */
    public function getQualifiedActivatedByColumn()
    {
        return $this->getTable() . '.' . $this->getActivatedByColumn();
    }

    /**
     * Get the name of the "activated at" column.
     *
     * @return string
     */
    public function getActivatedAtColumn()
    {
        return defined('static::ACTIVATED_AT') ? static::ACTIVATED_AT : config('activable.activated_at_column');
    }

    /**
     * Get the name of the "activated by" column.
     *
     * @return string
     */
    public function getActivatedByColumn()
    {
        return defined('static::ACTIVATED_BY') ? static::ACTIVATED_BY : config('activable.activated_by_column');
    }

    /**
     * Get the name of the "activated at" column.
     * Append "activated at" column to the attributes that should be converted to dates.
     *
     * @return string
     */
    public function getDates(){
        return array_merge(parent::getDates(), [$this->getActivatedAtColumn()]);
    }
}