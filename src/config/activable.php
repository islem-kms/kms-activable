<?php
return array(
    /*
    |--------------------------------------------------------------------------
    | Status column
    |--------------------------------------------------------------------------
    */
    'status_column' => 'status',

    /*
    |--------------------------------------------------------------------------
    | Activated At column
    |--------------------------------------------------------------------------
    */
    'activated_at_column' => 'activated_at',

    /*
    |--------------------------------------------------------------------------
    | Activated By column
    |--------------------------------------------------------------------------
    | Activated by column is disabled by default.
    | If you want to include the id of the user who activated a resource set
    | here the name of the column.
    | REMEMBER to migrate the database to add this column.
    */
    'activated_by_column' => null,

    /*
    |--------------------------------------------------------------------------
    | Strict Activation
    |--------------------------------------------------------------------------
    | If Strict Activation is set to true then the default query will return
    | only active resources.
    | In other case, all resources except Inactive ones, will returned as well.
    */
    'strict' => true,
);