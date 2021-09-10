@extends('layouts.base')

@section('title')
    {{ Auth::user()->warehouse->name }}
@endsection

@section('styles')
    <style>
        .bootstrap-datetimepicker-widget{
            margin: 0 !important;
            width: 100% !important;
        }
        .bootstrap-datetimepicker-widget table td.day>div{
            margin: 0 auto;
        }
    </style>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card-header">@lang('delivery.title')</div>
            <div class="form-group">
                <label class="label-control text-center w-100">@lang('delivery.datepicker')</label>
                <input class="form-control text-center datepicker" value="@if(session('date')){{session('date')->format('d/m/Y')}}@else{{date('d/m/Y')}}@endif" type="text" name="date">
            </div>
            <div class="deliveries-bloc"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function doGet(url, params) {
            params = params || {};
            $.get(url, params, function (response) {
                $('.deliveries-bloc').html(response);
            }).fail(function(){
                location.reload();
            });
        }

        $(function () {
            var path = '{{ route("deliveries-bloc") }}?date=';
            doGet(path + $("input[name=date]").val());

            $(".datepicker").datetimepicker().on('dp.change', function (ev) {
                doGet(path + $("input[name=date]").val());
            });

            $("body").on('click','.changeListState', function (ev) {
                waitingDialog.show('@lang('app.loading')');
                var id = $(this).data("id");
                var order = $(this).data("order");
                var state = $(this).data("state");
                var nextId = $(this).data("next-id");
                var reference = $(this).data("reference");

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route("deliveries-change-state-accordingly", ["id" => "_suivi_", "nextId" => "_nextsuivi_", "reference" => "_order_"]) }}"
                        .replace("_suivi_", id)
                        .replace("_nextsuivi_", nextId)
                        .replace("_order_", reference != "" ? reference : "null"),
                    type: 'POST',
                    data: "state=" + (state != "" ? state : "null"),
                    dataType: 'JSON',
                    success: function (response) {
                        location.reload();
                    },
                    complete: function(data) {
                        waitingDialog.hide();
                    }
                });
            });

            $("body").on('click','.saved-name', function (ev) {
                var name = $(this).data("name");
                $('.collapse').collapse("hide");
                $('#collapse' + name).collapse("show");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route("change-name") }}",
                    type: 'POST',
                    data: "name=" + name,
                    success: function (response) {
                        console.log("changed");
                    }
                });
            });

            $("body").on('dblclick','.btn-show-details', function (ev) {
                var url = $(this).data("url");

                window.location.href=url;
            });

            $("body").on('click','.btn-sms-sender', function (ev) {
                ev.preventDefault();
                var url = $(this).attr("href");
                waitingDialog.show('@lang('app.loading')');

                $.get(url, function (data) {
                    location.reload();
                });
            });
        });
    </script>
@endsection