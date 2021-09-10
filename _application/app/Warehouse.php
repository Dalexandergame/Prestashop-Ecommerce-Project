<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ps_warehouse';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_warehouse';

    /**
     * @var array
     */
    protected $fillable = ['name'];
}
