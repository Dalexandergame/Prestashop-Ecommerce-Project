<noscript>
    {literal}
        <style type="text/css">
            .noscript-form-container {
                display: none;
            }
        </style>
    {/literal}
    <div class="alert alert-danger">
        {l s="Vous n'avez pas activé javascript. Merci de l'activer." mod='tunnelvente'}
    </div>
</noscript>

<form action="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}module/tunnelvente/type"
      class="form_npa noscript-form-container" method="post">
    <h4>{l s="Enter the NPA" mod='tunnelvente'}</h4>
    <div class="npa-wrap">
        <input type="text" class="input_npa" maxlength="4" name="npa" placeholder="####" {if $hasSapin } readonly {/if}
               value="{if isset($npa)}{$npa}{/if}">
        {*<input  type="number" minlength="4" required class="input_npa" maxlength="4" name="npa" placeholder="####" value="{if isset($npa)}{$npa}{/if}">*}
        <button type="submit">{l s="Valider" mod='tunnelvente'}</button>
    </div>
    <div class="icon-tunnel">
        <div class="cercle_npa cercle"></div>
    </div>
    <br/>
    <p>
        {l s="Indicate the NPA or you want to have your ecosapin delivered" mod='tunnelvente'}
        <br/><br/>
        {l s="In Switzerland, the numbers consist of" mod='tunnelvente'} <span
                class="text-npa"><strong>{l s="four digits" mod='tunnelvente'}</strong>,
    {l s="a locality has its own NPA, for example:" mod='tunnelvente'} <strong>{l s="1052" mod='tunnelvente'}</strong> {l s="for the Mot-sur-lausanne" mod='tunnelvente'}</span>
    </p>

    <div class="btns_next_prev">
        <button type="submit" class="next">next</button>
    </div>

</form>

{literal}
    <script type="text/javascript">
        $(function ($) {
            //current
            $('.container_npa').removeClass('hidden');
            //next
            $('.container_type').addClass('hidden');
//        alert("theme");

            $('form.form_npa').submit(function (event) {
                event.preventDefault();
                var npa = $(".input_npa").val();
                if (npa == "" || npa.length != 4 || !$.isNumeric(npa)) {
                    alert("invalide NPA");
                    return false;
                }
                var $me = $(this), classe = 'isactive';
                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);
                    $("body").css("cursor", "progress");
                    try {
                        $.ajax({
                            type: 'POST',
                            url: $me.attr("action"),
                            data: 'ajax=1&' + $me.serialize(),
                            dataType: 'json',
                            success: function (json) {

                                if (json.hasError) {

                                    $.each(json.errors, function (k, v) {
                                        showError(v);
                                    });
                                } else {


                                    $('#resp_content').html(json.html);
                                    $('#my_errors').empty();
                                    ShowHideStep(json.numStep);
                                }
                                $me.removeClass(classe);
                                $("body").css("cursor", "default");
                            }
                        });
                    } catch (e) {
                        $me.removeClass(classe);
                        $("body").css("cursor", "default");
                    }

                }

            });
        });
    </script>
{/literal}
