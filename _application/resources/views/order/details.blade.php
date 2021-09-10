@extends('layouts.base')

@section('title')
    {{ Auth::user()->warehouse->name }}
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card-header">
                <div class="row">
                    <div class="input-group">
                        <select class="custom-select h-100" id="selectedState">
                            <option value="0" selected disabled>@lang("delivery.change_etat")</option>
                            @if($delivery->date_delivery === $date)
                                <option value="4">@lang("delivery.delivering")</option>
                                <option value="5">@lang("delivery.delivered")</option>
                            @elseif($delivery->date_retour === $date)
                                <option value="21">@lang("delivery.recovered")</option>
                                <option value="25">@lang("delivery.recovering")</option>
                            @endif
                        </select>
                        <div class="input-group-append">
                            <button id="changeState" class="text-center btn btn-primary m-0 px-3" type="button">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="delivery-details-bloc p-4">
                <div class="tim-typo">
                    @if($delivery->order->current_state == 5  || (!isset($delivery->order->current_state) && $delivery->active == 1))
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
                    <span class="tim-note">@lang('delivery.address')</span>
                </div>
                <div class="tim-typo">
                    <p class="text-muted m-0">
                        {{ $delivery->company }} {{ $delivery->address1 }},&nbsp;{{ $delivery->city }}&nbsp;{{ $delivery->postcode }}
                    </p>
                    <p class="text-muted m-0">
                        {{ $delivery->address2 }}
                    </p>
                    <p></p>
                </div>
                @if(trim($delivery->message) != "")
                    <div class="tim-typo">
                        <span class="tim-note">@lang('delivery.message')</span>
                        <p class="text-muted">
                            {{ $delivery->message }}
                        </p>
                    </div>
                @endif
                <div class="tim-typo">
                    <span class="tim-note">@lang('delivery.commande')</span>
                    <p class="text-muted m-0">
                        @lang('delivery.ref') {{ $order->reference}}
                    </p>
                    @foreach(explode(',', $delivery->commande) as $item)
                        @if(strpos($item, "Retour") === false)
                            <p class="text-muted m-0">
                                {{ $item }}
                            </p>
                        @endif
                    @endforeach
                    <p></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $("#changeState").click(function (ev) {
                var state = $("#selectedState option:selected").val();
                if (state != "0")
                    waitingDialog.show('@lang('app.loading')');

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route("deliveries-change-state", ["id" => $delivery->id_suivi_orders, "order" => isset($order->reference)? $order->reference: "null"]) }}",
                    type: 'POST',
                    data: "state=" + state,
                    dataType: 'JSON',
                    success: function (response) {
                        if (response.success == "1") {
                            location.reload();
                        } else {
                            alert("@lang("delivery.error")");
                            waitingDialog.hide();
                        }
                    }
                });
            });
        });
    </script>
@endsection