<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ps_employee';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_employee';

    /**
     * @var array
     */
    protected $fillable = ['lastname', 'firstname', 'email', 'passwd', 'id_warehouse', 'id_profile', 'active'];

    public function getAuthPassword(){
        return $this->passwd;
    }

    public function getName(){
        return $this->lastname . " " . $this->firstname;
    }

    public function warehouse()
    {
        return $this->hasOne('App\Warehouse', 'id_warehouse', 'id_warehouse');
    }
}
