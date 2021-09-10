<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_address
 * @property string $open_houre
 * @property string $receive_sms
 */
class Adresse extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ps_address';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_address';

    /**
     * @var array
     */
    protected $fillable = ['open_houre', 'receive_sms'];
}
