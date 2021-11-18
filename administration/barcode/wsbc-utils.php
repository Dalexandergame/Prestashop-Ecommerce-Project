<?php

/*********************************************************
 * Utility Methods for Webservice Barcode Client Examples
 *********************************************************/

define('SHIPPED_ORDER_STATE_ID', 24);
define('ID_CARRIER_POSTE', 7);

/**
 * A simple helper-Method for getting multiple elements (=objects or values) from SOAP Responses as an array
 * and also treating a single element as if it was an array.
 * Passing null results in an empty array.
 * @param $root the element data (=either a single object or value or an array of mutliple values or null)
 * @return an array containing the passed element(s) (or an empty array if the passed value was null)
 */
function getElements($root)
{
    if ($root == null) {
        return array();
    }
    if (is_array($root)) {
        return $root;
    } else {
        // simply wrap a single value or object into an array
        return array($root);
    }
}

/**
 * A simple helper method to transform an array of strings to a concatenated string with comma separation.
 * @param $strings an array containing string elements
 * @return a string with concatenated string elements separated by comma
 */
function toCommaSeparatedString($strings)
{
    $res       = "";
    $delimiter = "";
    foreach ($strings as $str) {
        $res       .= $delimiter . $str;
        $delimiter = ", ";
    }
    return $res;
}

function changeOrderStatus($order)
{
    // change status only if the carrier is POSTE
    if ($order->id_carrier != ID_CARRIER_POSTE)
        return;

    $order_state = new OrderState(SHIPPED_ORDER_STATE_ID);

    $current_order_state = $order->getCurrentOrderState();

    if ($current_order_state->id != $order_state->id) {
        // Create new OrderHistory
        $history              = new OrderHistory();
        $history->id_order    = $order->id;
        $history->id_employee = 1;//(int) Context::getContext()->employee->id;

        $use_existings_payment = false;
        if (!$order->hasInvoice())
            $use_existings_payment = true;
        $history->changeIdOrderState((int) $order_state->id, $order->id, $use_existings_payment);

        // synchronizes quantities if needed..
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            foreach ($order->getProducts() as $product) {
                if (StockAvailable::dependsOnStock($product['product_id']))
                    StockAvailable::synchronize($product['product_id'], (int) $product['id_shop']);
            }
        }

        $history->add();

    }
}
