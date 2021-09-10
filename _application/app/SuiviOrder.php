<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_suivi_orders
 * @property int $id_order
 * @property int $id_warehouse
 * @property int $id_carrier
 * @property int $id_carrier_retour
 * @property string $firstname
 * @property string $lastname
 * @property string $company
 * @property string $address1
 * @property string $address2
 * @property string $postcode
 * @property string $city
 * @property string $phone
 * @property string $phone_mobile
 * @property string $message
 * @property string $commande
 * @property int $position
 * @property boolean $active
 * @property string $date_delivery
 * @property string $date_retour
 * @property string $date_add
 * @property string $date_upd
 * @property boolean $to_translate
 */
class SuiviOrder extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'ps_suivi_orders';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_suivi_orders';

    protected $appends = [
        'order'
    ];

    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = ['id_suivi_orders', 'id_order', 'id_warehouse', 'id_carrier', 'id_carrier_retour', 'firstname', 'lastname', 'company', 'address1', 'address2', 'postcode', 'city', 'phone', 'phone_mobile', 'message', 'commande', 'position', 'active', 'date_delivery', 'date_retour', 'date_add', 'date_upd', 'to_translate', 'name', 'sms_received', 'sms1_received', 'sms2_received', 'sms3_received'];

    public function originalOrder()
    {
        return $this->hasOne('App\Order', 'id_order', 'id_order');
    }

    public function getOrderAttribute()
    {
        return $this->originalOrder ?? new Order();
    }
}
