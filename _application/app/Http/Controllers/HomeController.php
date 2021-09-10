<?php

namespace App\Http\Controllers;

use App\Http\Services\SmsFactor;
use App\Order;
use App\SuiviOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('cronSendSMS');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function deliveries_bloc(Request $request)
    {
        $this->validate($request, [
            'date' => 'required',
        ]);

        $date = \DateTime::createFromFormat('d/m/Y', $request->date);

        session(["date" => $date]);

        $user = Auth::user();

        $month = $date->format('m');
        $date  = $date->format('Y-m-d');
        $order = $month > 6 ? "position, position_retour" : "position_retour, position";

        $sql  = "select r.* from (
                  select
                    IF(soc.text is null, c.name, soc.text) as name,
                    CONCAT(c.name, LPAD(so.position, 3, 0)) as cname,
                    so.*
                  from ps_suivi_orders so
                    join ps_carrier c on c.id_carrier = so.id_carrier
                    left outer join ps_suivi_orders_carrier soc
                      on soc.id_carrier = so.id_carrier
                         and soc.date_delivery = so.date_delivery
                  where so.id_warehouse = {$user->id_warehouse} AND so.date_delivery = '$date' AND c.id_carrier != 7
                  UNION
                  select
                    IF(socr.text is null, c.name, socr.text) as name,
                    CONCAT(c.name, LPAD(so.position_retour, 3, 0)) as cname,
                    so.*
                  from ps_suivi_orders so
                    join ps_carrier c on c.id_carrier = so.id_carrier_retour
                    left outer join ps_suivi_orders_carrier socr
                      on socr.id_carrier = so.id_carrier_retour
                         and socr.date_delivery = so.date_retour
                  where so.id_warehouse = {$user->id_warehouse} AND so.date_retour = '$date' AND c.id_carrier != 7
                ) r
                left join ps_orders o on r.id_order = o.id_order 
                order by cname, $order
        ";
        $deliveries  = DB::select($sql);
        $_deliveries = [];
        $names       = [];
        $index       = 0;
        foreach ($deliveries as $key => $delivery) {
            if (!count($names) or $names[count($names) - 1]["name"] != $delivery->name) {
                $names[]  = ["name" => $delivery->name, "carrier" => $delivery->id_carrier];
                $commands = "";
                for ($i = $index; $i < $key; $i++) {
                    $commands .= $deliveries[$i]->commande . ',';
                }
                $commands = explode(',', rtrim($commands, ','));
                $recap    = array_map('trim', $commands);

                $missing = [];
                foreach ($recap as &$r) {
                    $c = substr($r, strpos($r, ' X') + 2, strlen($r));
                    $r = substr($r, 0, strpos($r, ' X'));
                    for ($i = 1; $i < $c; $i++) {
                        $missing[] = $r;
                    }
                }
                $recap = array_merge($recap, $missing);

                $total = array_count_values($recap);
                uksort($total, "strnatcasecmp");

                if (count($names) > 1) {
                    $names[count($names) - 2] = array_add($names[count($names) - 2], "count", count($recap));
                    $names[count($names) - 2] = array_add($names[count($names) - 2], "total", $total);
                }
                $index = $key;
            }
            $_deliveries[] = new SuiviOrder((array)$delivery);
        }
        if (count($names) > 0) {
            $commands = "";
            for ($i = $index; $i < count($deliveries); $i++) {
                $commands .= $deliveries[$i]->commande . ',';
            }
            $commands = explode(',', rtrim($commands, ','));
            $recap    = array_map('trim', $commands);

            $missing = [];
            foreach ($recap as &$r) {
                $c = substr($r, strpos($r, ' X') + 2, strlen($r));
                $r = substr($r, 0, strpos($r, ' X'));
                for ($i = 1; $i < $c; $i++) {
                    $missing[] = $r;
                }
            }
            $recap = array_merge($recap, $missing);

            $total = array_count_values($recap);
            uksort($total, "strnatcasecmp");
            $names[count($names) - 1] = array_add($names[count($names) - 1], "count", count($recap));
            $names[count($names) - 1] = array_add($names[count($names) - 1], "total", $total);
        }

        $deliveries = $_deliveries;

        $saved_name = session("saved_name");
        if (!isset($saved_name) && count($names)) {
            $saved_name = md5($names[0]["name"]);
            session(["saved_name" => $saved_name]);
        }
        return view('order._deliveries-bloc', compact("deliveries", "date", "names", "saved_name"));
    }

    function ddd($var)
    {
        if ((\Auth::user())->id_employee === 16) {
            var_dump($var);
            dd($var);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function deliveries()
    {
        return view('order.deliveries');
    }

    public function updateName(Request $request)
    {
        session(["saved_name" => $request->get("name")]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request, $id)
    {
        $this->validate($request, [
            'date' => 'required',
        ]);
        $date = $request->date;

        $delivery = SuiviOrder::all()->where('id_suivi_orders', $id)->first();

        $order = $delivery->order;
        return view('order.details', compact("delivery", "order", "date"));
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function change_state(Request $request, $id, $reference)
    {
        $this->validate($request, [
            'state' => 'required',
        ]);
        $state = $request->state;

        if ((int)$state == 0) {
            return response()->json([
                                        'success' => '0',
                                    ]);
        }

        $delivery = SuiviOrder::all()->where('id_suivi_orders', $id)->first();
        if ((int)$state == 5) {
            $delivery->active = 1;
        } elseif ((int)$state == 21) {
            $delivery->recovered = 1;
        } else {
            $delivery->active    = 0;
            $delivery->recovered = 0;
        }
        $delivery->save();

        $orders = Order::all()->where('reference', $reference);
        foreach ($orders as $order) {
            $order->current_state = $state;
            $order->save();

            DB::table('ps_order_history')->insert([
                                                      'id_employee'    => '0',
                                                      'id_order'       => $order->id_order,
                                                      'id_order_state' => $order->current_state,
                                                      'date_add'       => date("Y-m-d H:i:s"),
                                                  ]);
        }


        return response()->json([
                                    'success' => '1',
                                ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function change_state_accordingly(Request $request, $id, $nextId, $reference)
    {
        $this->validate($request, [
            'state' => 'required',
        ]);

        $delivery = SuiviOrder::all()->where('id_suivi_orders', $id)->first();
        $date     = session('date')->format('Y-m-d');
        if ($delivery->date_delivery === $date) {
            $delivery->active    = 1;
            $delivery->recovered = 0;
            $state               = 5;

            if($nextId != -1) {
                $nextDelivery = SuiviOrder::all()->where('id_suivi_orders', $nextId)->first();
                $phone        = trim(str_replace([" ", ".", "(0)"], "", $nextDelivery->phone_mobile));

                if ($nextDelivery->order->address->receive_sms == '1') {
                    if (strlen($phone) && substr_count($phone, "0") != strlen($phone) && $nextDelivery->sms2_received == 0 && $nextDelivery->id_carrier != 7) {
                        $result = json_decode(SmsFactor::send(trans("delivery.sms2"), [$phone]));
                        $response[] = $result;
                        if($result->sent == 1) {
                            $nextDelivery->sms2_received = 1;
                            $nextDelivery->save();
                        }
                    }
                }
            }
        } elseif ($delivery->date_retour === $date) {
            $delivery->active    = 1;
            $delivery->recovered = 1;
            $state               = 21;
        } else {
            return response()->json([
                                        'success' => '0',
                                    ]);
        }
        $delivery->save();

        $orders = Order::all()->where('reference', $reference);
        foreach ($orders as $order) {
            if ($order->current_state == $state) continue;
            $order->current_state = $state;
            $order->save();

            DB::table('ps_order_history')->insert([
                                                      'id_employee'    => '0',
                                                      'id_order'       => $order->id_order,
                                                      'id_order_state' => $order->current_state,
                                                      'date_add'       => date("Y-m-d H:i:s"),
                                                  ]);
        }

        return response()->json([
                                    'success' => '1'
                                ]);
    }

    /**
     * @return array
     */
    public function sendSMS(Request $request,$number)
    {
        $response = ["SMS:"];
        $timezone = new \DateTimeZone('Europe/Zurich');

        switch ($number) {
            case 1: //à envoyer la veille de la livraison et retour, à 16h.
                $date = $request->query('date');
                if(isset($date))
                    $date = \DateTime::createFromFormat('Y-m-d', $date);
                else
                    $date = new \DateTime();

                $date->setTimezone($timezone);
                $date = $date->modify('next day')->format('Y-m-d');
                $carrier = $request->query('carrier');

                $deliveries = SuiviOrder::where('id_carrier', $carrier)
                    ->where(function ($query) use ($date) {
                        return $query->where('date_delivery', $date)->orWhere('date_retour', $date);
                    })
                    ->get();

                foreach ($deliveries as $delivery) {
                    $sms   = $delivery->date_delivery === $date? "sms1": "sms3";
                    $phone = trim(str_replace([" ", ".", "(0)"], "", $delivery->phone_mobile));
                    if ($delivery->order->address->receive_sms == '1' && strlen($phone) && substr_count($phone, "0") != strlen($phone) && $delivery->{$sms."_received"} == 0 && $delivery->id_carrier != 7) {
                        $result = json_decode(SmsFactor::send(trans("delivery.$sms"), [$phone]));
                        $response[] = $result;
                        if($result->sent == 1) {
                            $delivery->{$sms."_received"} = 1;
                            $delivery->save();
                        }
                    }
                }
                break;
            case 2: //à envoyer le jour de la livraison, les 3 premiers clients de la liste reçoivent un SMS à 7h30
                $date = $request->query('date');
                if(!isset($date)){
                    $datetime = new \DateTime();
                    $datetime->setTimezone($datetime);

                    $date = $datetime->format("Y-m-d");
                }

                $carrier = $request->query('carrier');
                $deliveries = SuiviOrder::where('date_delivery', $date)
                    ->where('id_carrier', $carrier)
                    ->orderBy('id_warehouse')
                    ->orderBy('position')
                    ->orderBy('position_retour')
                    ->get();

                $count      = 0;
                $warehouse  = null;
                foreach ($deliveries as $delivery) {
                    $phone = trim(str_replace([" ", ".", "(0)"], "", $delivery->phone_mobile));
                    if ($warehouse != $delivery->id_warehouse) {
                        $warehouse = $delivery->id_warehouse;
                        $count     = 0;
                    }
                    if ($delivery->order->address->receive_sms == '1' && $count < 3) {
                        if (strlen($phone) && substr_count($phone, "0") != strlen($phone) && $delivery->sms2_received == 0 && $delivery->id_carrier != 7) {
                            $result = json_decode(SmsFactor::send(trans("delivery.sms2"), [$phone]));
                            $response[] = $result;
                            if($result->sent == 1) {
                                $delivery->sms2_received = 1;
                                $delivery->save();
                            }
                        }
                        $count++;
                    }
                }
                break;
            case 3:
                $delivery = $request->query('delivery');
                $sms = $request->query('sms');

                $deliveries = SuiviOrder::where($sms."_received", 0)
                    ->where('id_suivi_orders', $delivery)
                    ->get();

                foreach ($deliveries as $delivery) {
                    $phone = trim(str_replace([" ", ".", "(0)"], "", $delivery->phone_mobile));

                    if ($delivery->order->address->receive_sms == '1') {
                        if (strlen($phone) && substr_count($phone, "0") != strlen($phone) && $delivery->{$sms."_received"} == 0 && $delivery->id_carrier != 7) {
                            $result = json_decode(SmsFactor::send(trans("delivery.$sms"), [$phone]));
                            $response[] = $result;
                            if($result->sent == 1) {
                                $delivery->{$sms . "_received"} = 1;
                                $delivery->save();
                            }
                        }
                    }
                }
                break;
        }
        return $response;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function cronSendSMS()
    {
        //à envoyer la veille de la livraison et retour, à 16h.
        $date     = new \DateTime();
        $timezone = new \DateTimeZone('Europe/Zurich');
        $response = ["SMS:"];

        $date->setTimezone($timezone);

        $date       = $date->modify('next day')->format('Y-m-d');
        $deliveries = SuiviOrder::where(function($query) use ($date) {
            return $query->where('date_delivery', $date)->orWhere('date_retour', $date);
        })->get();

        foreach($deliveries as $delivery) {
            $sms   = $delivery->date_delivery === $date ? "sms1" : "sms3";
            $phone = trim(str_replace([" ", ".", "(0)"], "", $delivery->phone_mobile));

            if(
                $delivery->order->address->receive_sms == '1' &&
                strlen($phone) && substr_count($phone, "0") !=
                strlen($phone) && $delivery->{$sms . "_received"} == 0 &&
                $delivery->id_carrier != 7) {
                $result     = json_decode(SmsFactor::send(trans("delivery.$sms"), [$phone]));
                $response[] = $result;

                if($result->sent == 1) {
                    $delivery->{$sms . "_received"} = 1;
                    $delivery->save();
                }
            }
        }
        return $response;
    }
}
