@extends("layouts.base")

@section("title")
    @lang('carrier.title')
@endsection

@section("content")
    <div class="row">
        <form class="form" method="" action="" style=" margin:  0 auto; padding: 20%;">
            <div class="form-group bmd-form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="material-icons">face</i></span>
                    </div>
                    <input type="email" name="email" class="form-control" placeholder="@lang("auth.email")..." value="{{ old("email") }}">
                </div>
            </div>
            <div class="form-group bmd-form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="material-icons">lock_outline</i></span>
                    </div>
                    <input type="password" name="password" placeholder="@lang("auth.password")..." class="form-control">
                </div>
            </div>
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" value="true" checked="">
                    <span class="form-check-sign"><span class="check"></span></span> @lang("auth.rememberMe")
                </label>
            </div>
            <div class="text-center">
                <a href="#pablo" class="btn btn-primary btn-round">@lang("auth.connect")</a>
            </div>
        </form>
    </div>
@endsection
