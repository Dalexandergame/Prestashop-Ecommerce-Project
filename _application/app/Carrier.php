<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id_carrier
 * @property int $id_reference
 * @property int $id_tax_rules_group
 * @property string $name
 * @property string $url
 * @property boolean $active
 * @property boolean $deleted
 * @property boolean $shipping_handling
 * @property boolean $range_behavior
 * @property boolean $is_module
 * @property boolean $is_free
 * @property boolean $shipping_external
 * @property boolean $need_range
 * @property string $external_module_name
 * @property int $shipping_method
 * @property int $position
 * @property int $max_width
 * @property int $max_height
 * @property int $max_depth
 * @property float $max_weight
 * @property int $grade
 */
class Carrier extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'ps_carrier';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'id_carrier';

    /**
     * @var array
     */
    protected $fillable = ['id_reference', 'id_tax_rules_group', 'name', 'url', 'active', 'deleted', 'shipping_handling', 'range_behavior', 'is_module', 'is_free', 'shipping_external', 'need_range', 'external_module_name', 'shipping_method', 'position', 'max_width', 'max_height', 'max_depth', 'max_weight', 'grade'];

}
