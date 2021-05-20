<?php

/**
 * User: mouhcine@pulse.digital
 * Date: 05/10/2017
 * Time: 16:45
 */
class Partner extends ObjectModel
{
    public $partner_id, $name, $img, $description, $warehouse_id;

    public static $definition = array(
        'table' => 'partners',
        'primary' => 'partner_id',
        'fields' => [
            'partner_id'  => ['type' => self::TYPE_INT],
            'name'  => ['type' => self::TYPE_STRING],
            'img'  => ['type' => self::TYPE_STRING],
            'description'  => ['type' => self::TYPE_STRING],
            'warehouse_id' => ['type' => self::TYPE_INT]
        ]
    );
}
