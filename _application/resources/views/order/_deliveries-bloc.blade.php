@if(count($deliveries))
    <style>
        .rowButtons {
            height: 40px;
            float: right;
            border-radius: 0;
        }

        .pinnedButtons {
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            position: absolute;
            border-radius: 0;
        }

        .livraison, .retour {
            margin: 0 !important;
        }

        li a.active {
            color: #1e88e5 !important;
        }
    </style>
    <div class="container" style="margin: 0">
        <ul class="nav nav-pills nav-stacked p-0">
            @foreach($names as $name)
                <li @if(md5($name['name']) == $saved_name) class="active"
                    @endif style="width: 100%;padding-bottom: 5px;">
                    <a data-toggle="pill" href="#{{md5($name['name'])}}" data-name="{{md5($name['name'])}}"
                       class="btn-link saved-name @if(md5($name['name']) == $saved_name) active @endif"
                       style="color: #495057">
                        <strong>{{$name['name']}}:</strong> {{$name['count']}} produits <br>

                        <div class="collapse" id="collapse{{md5($name['name'])}}">
                            <div class="card card-body" style="margin: 0;padding: 10px;box-shadow: unset">
                                @foreach($name['total'] as $key => $item)
                                    <div class="d-flex justify-content-between mx-5">
                                        <div>{{$key}}</div>
                                        <div>{{$item}}</div>
                                    </div> <br/>
                                @endforeach
                            </div>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <hr>
    <div class="tab-content">
        @foreach($names as $name)
            <div id="{{md5($name['name'])}}"
                 class="tab-pane fade @if(md5($name['name']) == $saved_name) in active show @endif">
                <div style="padding: 0;">
                    @if($deliveries[0]->date_delivery === $date)
                    <a href="{{ route("sms-sender", ['number' => 2]) }}?carrier={{ $name['carrier'] }}&date={{ $date }}"
                            style="background-color: @if($deliveries[0]->sms2_received == 1) #606060 @else #4d7910 @endif !important;width: 100%;margin-right: 0;color: #fff;" class="btn btn-primary m-x-2 rowButtons btn-sms-sender">
                        @lang('delivery.sendFirstSMS')
                    </a>
                    @endif
                    @foreach($deliveries as $key => $delivery)
                        {{--{{$delivery->name}}--}}
                        @if($delivery->name == $name['name'])
                            <a href="#collapseExample" data-toggle="collapse" aria-expanded="true"
                               aria-controls="collapseExample"
                               style="box-shadow: @if($delivery->date_delivery === $date)#4d7910 @else #276c9f @endif 0px 2px 2px;margin-bottom: 10px; ">
                                <div class="list-group-item list-group-item-action" style="height: 40px;padding: 0;">
                                    <div class="col" style="position: relative;padding: 0;margin: 0;">
                                        <p style="display: inline;padding: 12px;">
                                            {{ $delivery->lastname }} {{ $delivery->firstname }} {{ $delivery->order->address->company? "- " . $delivery->order->address->company: "" }} {{ $delivery->order->address->open_houre? $delivery->order->address->open_houre: "" }}
                                        </p>
                                        @if($delivery->order->current_state == 5 || (!isset($delivery->order->current_state) && $delivery->active == 1))
                                            <span class="badge float-right"
                                                  style="background-color: #DDFFAA;color: #000;">@lang("delivery.delivered")</span>
                                        @elseif($delivery->order->current_state == 4)
                                            <span class="badge float-right"
                                                  style="background-color: #EEDDFF;color: #000;">@lang("delivery.delivering")</span>
                                        @elseif($delivery->order->current_state == 25)
                                            <span class="badge float-right"
                                                  style="background-color: #A8CCFF;color: #000;">@lang("delivery.recovering")</span>
                                        @elseif($delivery->order->current_state == 21 || (!isset($delivery->order->current_state) && $delivery->recovered == 1))
                                            <span class="badge float-right"
                                                  style="background-color: #92ACEA;color: #000;">@lang("delivery.recovered")</span>
                                        @else
                                            <span class="badge float-right"
                                                  style="background-color: #7BEA7B;color: #000;">@lang("delivery.other")</span>
                                        @endif
                                    </div>
                                    <div class="col-3 p-0">
                                        <button class="btn btn-primary m-0 p-2 rowButtons btn-show-details"
                                                type="button"
                                                style="background-color: @if($delivery->date_delivery === $date)#4d7910 @else #276c9f @endif !important"
                                                data-toggle="collapse"
                                                data-target="#collapseExample{{ $delivery->id_suivi_orders }}"
                                                data-url="{{ route('deliveries-detail', ["id" => $delivery->id_suivi_orders]) }}?date={{ $date }}"
                                                aria-expanded="false"
                                                aria-controls="collapseExample{{ $delivery->id_order }}">
                                            <i class="fa fa-plus" style="margin: 0;"></i>
                                        </button>
                                        <button onclick="window.open('http://maps.apple.com?q={{ $delivery->address1 . " " . $delivery->postcode . " " . $delivery->city . ", Suisse" }}','_blank')"
                                                style="background-color: @if($delivery->date_delivery === $date)#4d7910 @else #276c9f @endif !important"
                                                class="btn btn-primary m-0 p-2 rowButtons" type="button"><i
                                                    class="fa fa-map-marker" style="margin: 0;"></i></button>
                                        <button data-order="{{ $delivery->id_order }}"
                                                data-id="{{ $delivery->id_suivi_orders }}"
                                                data-next-id="{{ count($deliveries) > $key + 3? $deliveries[$key + 3]->id_suivi_orders: -1 }}"
                                                data-reference="{{ $delivery->order->reference }}"
                                                data-state="{{ $delivery->order->current_state }}"
                                                style="background-color: @if($delivery->date_delivery === $date)#4d7910 @else #276c9f @endif !important"
                                                class="btn btn-primary m-0 p-2 rowButtons changeListState"
                                                type="button">
                                            <i class="fa fa-check" style="margin: 0;"></i></button>
                                    </div>
                                </div>
                                <div class="collapse" id="collapseExample{{ $delivery->id_suivi_orders }}">
                                    <div class="" style="padding:  20px;position: relative;">
                                        <div class="tim-typo">
                                            <span class="tim-note"
                                                  style="color: #4d7910;">@lang('delivery.address')</span>
                                            <p class="text-muted m-0">
                                                 {{ $delivery->company }} {{ $delivery->address1 }},&nbsp;{{ $delivery->city }}
                                                &nbsp;{{ $delivery->postcode }}
                                            </p>
                                            <p class="text-muted m-0">
                                                {{ $delivery->address2 }}
                                            </p>
                                            @if(trim($delivery->message) != "")
                                                <span class="tim-note"
                                                      style="color: #4d7910;">@lang('delivery.message')</span>
                                                <p class="text-muted m-0">
                                                    {{ $delivery->message }}
                                                </p>
                                            @endif
                                            <span class="tim-note"
                                                  style="color: #4d7910;">@lang('delivery.commande')</span>
                                            <p class="text-muted m-0">
                                            @foreach(explode(',', $delivery->commande) as $item)
                                                @if(strpos($item, "Retour") === false)
                                                    <p class="text-muted m-0">
                                                        {{ $item }}
                                                    </p>
                                                @endif
                                            @endforeach
                                            <p></p>
                                        </div>
                                        <div class="dropdown" style="position: unset">
                                            <a style="color: #fff;background-color: @if($delivery->date_delivery === $date)#4d7910 @else #276c9f @endif !important"
                                                    id="dropdownMenuLink" role="button"
                                                    class="btn btn-primary m-0 p-2 pinnedButtons dropdown-toggle"
                                                    type="button" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="true">
                                                <i class="fa fa-envelope"></i>
                                            </a>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a class="dropdown-item btn-sms-sender" @if($delivery->sms1_received == 1) style="background-color: #9ccc65" @endif
                                                   href="{{ route("sms-sender", ['number' => 3]) }}?delivery={{ $delivery->id_suivi_orders }}&sms=sms1">1: @lang('delivery.sendSMS1')</a>
                                                <a class="dropdown-item btn-sms-sender" @if($delivery->sms2_received == 1) style="background-color: #9ccc65" @endif
                                                   href="{{ route("sms-sender", ['number' => 3]) }}?delivery={{ $delivery->id_suivi_orders }}&sms=sms2">2: @lang('delivery.sendSMS2')</a>
                                                <a class="dropdown-item btn-sms-sender" @if($delivery->sms3_received == 1) style="background-color: #9ccc65" @endif
                                                   href="{{ route("sms-sender", ['number' => 3]) }}?delivery={{ $delivery->id_suivi_orders }}&sms=sms3">3: @lang('delivery.sendSMS3')</a>
                                            </div>
                                        </div>
                                        <button onclick="window.location.href='tel:{{ trim($delivery->phone_mobile) != ""? $delivery->phone_mobile: $delivery->phone }}'"
                                                style="right: 30px;background-color: @if($delivery->date_delivery === $date)#4d7910 @else #276c9f @endif !important"
                                                class="btn btn-primary m-0 p-2 pinnedButtons" type="button"><i
                                                    class="fa fa-phone"></i>
                                        </button>
                                        <button onclick="window.location.href='{{ route('deliveries-detail', ["id" => $delivery->id_suivi_orders]) }}?date={{ $date }}'"
                                                style="right: 60px;background-color: @if($delivery->date_delivery === $date)#4d7910 @else #276c9f @endif !important"
                                                class="btn btn-primary m-0 p-2 pinnedButtons" type="button"><i
                                                    class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center">
        @lang('delivery.empty')
    </div>
@endif