<?php


class SuiviOrder extends ObjectModel
{

    /** @var integer Object id */
    public $id;

    public $id_order;
    public $id_warehouse;
    public $id_carrier;
    public $id_carrier_retour;
    public $firstname;
    public $lastname;
    public $company;
    public $address1;
    public $address2;
    public $postcode;
    public $city;
    public $phone;
    public $phone_mobile;
    public $message;
    public $active;
    public $recovered;
    public $date_delivery;
    public $date_retour;
    public $date_add;
    public $date_upd;
    public $position;
    public $position_retour;

    protected $tables = array('suivi_orders');
    protected $table = 'suivi_orders';
    protected $identifier = 'id_suivi_orders';

    public $commande;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'suivi_orders',
        'primary' => 'id_suivi_orders',
        'multilang' => false,
        'fields' => array(
            'id_carrier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false),
            'id_carrier_retour' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false),
            'id_warehouse' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false),
            'address1' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'address2' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'postcode' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'city' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'phone_mobile' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'message' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'commande' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'date_delivery' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_retour' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'lastname' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'company' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => false),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'recovered' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'position' => array('type' => self::TYPE_INT),
            'position_retour' => array('type' => self::TYPE_INT),
        )
    );

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }


    public function update($null_values = false)
    {
        $this->updateOrderDetailWarehouse($this->id_order, $this->id_warehouse);
        $this->updateOrderCarrier($this->id_order, $this->id_carrier);

        return parent::update($null_values);
    }

    public function updateOrderDetailWarehouse($id_order, $id_warehouse)
    {

        Db::getInstance()->execute('
		UPDATE `' . _DB_PREFIX_ . 'order_detail`
		SET id_warehouse = ' . $id_warehouse . '		
                WHERE id_order = ' . (int)$id_order);
    }

    public function updateOrderCarrier($id_order, $id_carrier)
    {

        Db::getInstance()->execute('
		UPDATE `' . _DB_PREFIX_ . 'orders`
		SET id_carrier = ' . $id_carrier . '		
                WHERE id_order = ' . (int)$id_order);
    }

    public function toggleStatus()
    {
        return parent::toggleStatus();
    }

    /**
     * Toggle object status in database
     *
     * @return boolean Update result
     */
    public function toggleRecoveredStatus($status)
    {
        // Object must have a variable called 'active'
        if (!array_key_exists('recovered', $this))
            throw new PrestaShopException('property "recovered" is missing in object ' . get_class($this));

        // Update only active field
        $this->setFieldsToUpdate(array('recovered' => true));

        // Update active status on object
        $this->recovered = $status;

        // Change status to active/inactive
        return $this->update(false);
    }

    /**
     * Toggle object status in database
     *
     * @return boolean Update result
     */
    public function toggleDeliveredStatus($status)
    {
        // Object must have a variable called 'active'
        if (!array_key_exists('active', $this))
            throw new PrestaShopException('property "active" is missing in object ' . get_class($this));

        // Update only active field
        $this->setFieldsToUpdate(array('active' => true));

        // Update active status on object
        $this->active = $status;

        // Change status to active/inactive
        return $this->update(false);
    }

    public function setNewCarrier($id_carrier)
    {
        // $this->updateOrderCarrier($this->id_order,$id_carrier);

        return Db::getInstance()->execute('
		UPDATE `' . _DB_PREFIX_ . 'suivi_orders`
		SET id_carrier = ' . $id_carrier . '		
                WHERE id_suivi_orders = ' . (int)$this->id);
    }

    public function setNewCarrierRetour($id_carrier)
    {
        return Db::getInstance()->execute('
		UPDATE `' . _DB_PREFIX_ . 'suivi_orders`
		SET id_carrier_retour = ' . $id_carrier . '		
                WHERE id_suivi_orders = ' . (int)$this->id);
    }


    public function updatePosition($way, $position, $id_suivi_orders = null)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT `position`, `id_suivi_orders`
			FROM `' . _DB_PREFIX_ . 'suivi_orders`
			WHERE `id_suivi_orders` = ' . (int)($id_suivi_orders ? $id_suivi_orders : $this->id) . '
			ORDER BY `position` ASC'
        ))
            return false;

        foreach ($res as $suivi_orders)
            if ((int)$suivi_orders['id_suivi_orders'] == (int)$this->id)
                $moved_feature = $suivi_orders;

        if (!isset($moved_feature) || !isset($position))
            return false;

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'suivi_orders`
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($way
                    ? '> ' . (int)$moved_feature['position'] . ' AND `position` <= ' . (int)$position
                    : '< ' . (int)$moved_feature['position'] . ' AND `position` >= ' . (int)$position))
            && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'suivi_orders`
			SET `position` = ' . (int)$position . '
			WHERE `id_suivi_orders`=' . (int)$moved_feature['id_suivi_orders']));

    }

}

?>
