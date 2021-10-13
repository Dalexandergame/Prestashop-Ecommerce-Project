<?php

/**
 * PlanningDeliveries class, PlanningDeliveries.php
 * Planning deliveries management
 * @category classes
 *
 *
 */
class PlanningDeliveriesByCarrierOver extends PlanningDeliveriesByCarrier {

    /** @var datetime retour Date */
    public $date_retour;
    protected $fieldsValidate = array('id_cart' => 'isUnsignedId', 'id_order' => 'isUnsignedId', 'id_planning_delivery_carrier_slot' => 'isUnsignedId', 'date_delivery' => 'isDate', 'date_retour' => 'isDate');

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
        return $fields;
    }

}

?>
