<h1>Viewing a partner "{$partner->name}"</h1>
<div class="col-md-3 col-md-offset-4">
    <h2>{$partner->name}</h2>
    <img src="{$modulePath}/uploads/{$partner->img}" class="img-responsive" />
    <hr>
    <p>
        {$partner->description}
    </p>
    <hr>
    <a href="{$goBackUrl}"
       class="btn btn-primary"><= go back to settings</a>
</div>