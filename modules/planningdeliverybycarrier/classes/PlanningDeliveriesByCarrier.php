<?php

/**
* PlanningDeliveries classes, PlanningDeliveries.php
* Planning deliveries management
* @category classes
*
* @author Roturier Alexandre <alexandre.roturier@gmail.com>
* @copyright Roturier Alexandre
* @version 1.0
*
*/

class PlanningDeliveriesByCarrier extends ObjectModel
{
	/** @var integer Object id */
	public $id;
	/** @var int ID Cart */
	public $id_cart;
	/** @var int ID Order */
	public $id_order;
	/** @var integer Default group ID */
	public $id_planning_delivery_carrier_slot;
	/** @var datetime Delivery Date */
	public $date_delivery;
	/** @var string Object creation date */
	public $date_add;
	/** @var string Object last modification date */
	public $date_upd;
	public $test_date;
	protected $tables = array ('planning_delivery_carrier');
	protected $fieldsRequired = array('date_delivery');
//	protected $fieldsValidate = array('id_cart' => 'isUnsignedId', 'id_order' => 'isUnsignedId', 'id_planning_delivery_carrier_slot' => 'isUnsignedId', 'date_delivery' => 'isDate');
	protected $table = 'planning_delivery_carrier';
	protected $identifier = 'id_planning_delivery_carrier';

    /** @var datetime retour Date */
    public $date_retour;
    protected $fieldsValidate = array('id_cart' => 'isUnsignedId', 'id_order' => 'isUnsignedId', 'id_planning_delivery_carrier_slot' => 'isUnsignedId', 'date_delivery' => 'isDate');//, 'date_retour' => 'isDate'

    public function getFields() {
        $fields = array();
        parent::validateFields();
        $fields['id_cart'] = pSQL($this->id_cart);
        $fields['id_order'] = (int) ($this->id_order);
        $fields['id_planning_delivery_carrier_slot'] = (int) ($this->id_planning_delivery_carrier_slot);
        $fields['date_delivery'] = pSQL($this->date_delivery);
        $fields['date_retour'] = pSQL($this->date_retour);
        $fields['date_add'] = pSQL($this->date_add);
        $fields['date_upd'] = pSQL($this->date_upd);
        $fields['test_date'] = pSQL($this->test_date);
        return $fields;
    }
	public function __construct($id = null, $id_lang = null)
	{
		parent::__construct($id, $id_lang);
	}
}

?>
