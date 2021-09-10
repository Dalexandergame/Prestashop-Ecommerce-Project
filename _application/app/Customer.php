<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_customer
 * @property string $open_houre
 */
class Customer extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ps_customer';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_customer';

    /**
     * @var array
     */
    protected $fillable = ['open_houre'];
}
