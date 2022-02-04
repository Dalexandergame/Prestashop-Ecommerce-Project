<?php
/**
 * Created by IntelliJ IDEA.
 * User: Simo.Pulse
 * Date: 10/08/2018
 * Time: 09:12
 */

namespace App\Http\Services;


class SmsFactor
{

    public static function send($message, $numbers, $id_shop)
    {
        $recipients = array();
        foreach ($numbers as $number) {
            $recipients[] = array('value' => $number);
        }
        if ($id_shop == 3) $shop_name = "My Abies";
        elseif ($id_shop == 2) $shop_name = "Ecosapin FR";
        else $shop_name = "Ecosapin CH";
        $postdata = array(
            'sms' => array(
                'message' => array(
                    'text' => $message,
                    'sender' => $shop_name,
                ),
                'recipients' => array(
                    'gsm' => $recipients
                )
            )
        );

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxNjU1MSIsImlhdCI6MTUzMzg4ODU4OX0.X1okrbD11OTm4NY8alYYrxl9SBPpfhiSnYwy_B70RnU'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.smsfactor.com/send");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}