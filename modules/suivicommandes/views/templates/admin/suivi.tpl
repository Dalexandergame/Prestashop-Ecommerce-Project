
{*<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css"
      rel="stylesheet"/>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
      
      *}
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

{if isset($alert)}
    <div class="alert alert-danger">{$alert}</div>
{/if}

<h2>{l s='Page suivi de commandes' mod='suivicommandes'}</h2>
<form id="form" method="post" action="index.php?controller=AdminSuiviCommandes&token={$token}" class="form-inline">
    <div class="form-group">
        {l s='Date de livraison' mod='suivicommandes'} :
        <input type="text" name="datepickerDatelivraison" id="datepickerDatelivraison" class="form-control"/>
        {l s='Entrepôt' mod='suivicommandes'} :
    </div>

    <div class="form-group">
        <select name="warehouse_selected[]" multiple="" id="warehouse_selected" class="form-control">
            {foreach from=$warehouses item=warehouse}
                <option value="{$warehouse.id}" {$warehouse.selected}> {$warehouse.name}</option>
            {/foreach}
        </select>
    </div>

    <input type="submit" class="btn btn-info" name="submitImport" value="{l s='OK' mod='suivicommandes'}"/>

    {if !$restricted}
        <input type="submit" class="btn btn-info" name="submitMaj" value="{l s='Mettre à jour' mod='suivicommandes'}"/>
        <input type="submit" class="btn btn-info" name="ordonnerOSM"
               value="{l s='Ordonner selon OSM' mod='suivicommandes'}"/>
        <a href="" id="voirMap" class="btn btn-info" role="button"
           target="_blank">{l s='Voir Map' mod='suivicommandes'}</a>
    {/if}

    <br><br>

    <div id="blockc" class="row">
      {include file='./blockc.tpl' blocks=$blocks} 
    </div>

    <br><br>
    {if isset($lists)}
        <fieldset class="form-group">
            <legend>Modification en masse des transporteurs:</legend>
            {if $carriers}
                <select name="carrier_selected" class="form-control" id="carrier_selected_original">
                    {foreach from=$carriers item=carrier}
                        <option value="{$carrier.id_carrier}"> {$carrier.name}</option>
                    {/foreach}
                </select>
            {/if}
            <select name="carrier_type" class="form-control" id="carrier_type_original">
                <option value="L"> Livraison</option>
                <option value="R"> Retour</option>
            </select>
        </fieldset>
        <br>
        <br>
        <div>
            {$lists}
        </div>
        <div style="clear: both"></div>
        <div class="row form-inline">
            <fieldset class="form-group">
                <legend>Modification en masse des transporteurs:</legend>
                {if $carriers}
                    <select name="carrier_selected" class="form-control" id="carrier_selected_clone">
                        {foreach from=$carriers item=carrier}
                            <option value="{$carrier.id_carrier}"> {$carrier.name}</option>
                        {/foreach}
                    </select>
                {/if}
                <select name="carrier_type" class="form-control" id="carrier_type_clone">
                    <option value="L"> Livraison</option>
                    <option value="R"> Retour</option>
                </select>
            </fieldset>
            <br><br>
            <div style="clear: both"></div>
        </div>
        <script>
            {*Implement data binding between duplicated feilds*}
            $('#carrier_selected_original').change(function (e) {
                $('#carrier_selected_clone').val(e.target.value);
            });
            $('#carrier_type_original').change(function (e) {
                $('#carrier_type_clone').val(e.target.value);
            });
            $('#carrier_selected_clone').change(function (e) {
                $('#carrier_selected_original').val(e.target.value);
            });
            $('#carrier_type_clone').change(function (e) {
                $('#carrier_type_original').val(e.target.value);
            });
        </script>
    {/if}
</form>

<script type="text/javascript">

    $(document).ready(function () {

        $("#datepickerDatelivraison").datepicker({
            prevText: '',
            nextText: '',
            currentText: "Now",
            dateFormat: 'yy-mm-dd'
        });
        {if $dateLivraison}
        $("#datepickerDatelivraison").datepicker("setDate", "{$dateLivraison}");
        {else}
        $("#datepickerDatelivraison").datepicker("setDate", new Date());
        {/if}

        $("td.delivered a, td.recovered a").click(function (e) {
            e.preventDefault();
            $("body").css("cursor", "progress");

            $element = $(this);
            $url = $element.attr("href");
            if ($element.hasClass("action-enabled")) {
                $url += "&status=0"
            } else {
                $url += "&status=1"
            }

            $.post($url, function (data) {
                $element.toggleClass("action-enabled");
                $element.toggleClass("action-disabled");
                $element.children("i.icon-check").toggleClass("hidden");
                $element.children("i.icon-remove").toggleClass("hidden");
            }).complete(function () {
                $("body").css("cursor", "default");
            });
        });

        $("#voirMap").click(function () {
            $(this).attr('href', function () {
                return 'index.php?controller=AdminSuiviCommandes&token={$token}&map&date=' + $('#datepickerDatelivraison').val() + "&wh[]=" + $('#warehouse_selected').val();
            });
        });

        {if !$restricted}
        $('.newPosition').editable({
            success: function (response) {
                response = JSON.parse(response);
                if (response.success == 'true') {
                    //ok behaviour
                }
            }

        });

        $('.carriers').editable({
            {if $carriers}
            source: [
                {foreach from=$carriers item=carrier}
                {/foreach}
            ],
            {/if}
            params: function (params) {
                //originally params contain pk, name and value
                params.date = $('#datepickerDatelivraison').val();
                params.wh = $('#warehouse_selected').val();
                return params;
            },
            success: function (response, newValue) {
                if (response.status == 'error') return response.msg; //msg will be shown in editable form
                else {
                    $("#blockc").empty();
                    $("#blockc").html(response);
                }
            }

        });
        {/if}

    });
</script>
