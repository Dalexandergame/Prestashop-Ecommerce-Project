<noscript>
    {literal}
        <style type="text/css">
            .noscript-form-container {
                display: none;
            }
        </style>
    {/literal}
    <div class="alert alert-danger">
        {l s="Vous n'avez pas activé javascript. Merci de l'activer." mod='tunnelventeabies'}
    </div>
</noscript>
{if $isTunnelEnabled}
    <form action="{$urls.base_url}module/tunnelventeabies/type"
          class="form_npa noscript-form-container" method="post">
        <span class="tunnelVenteHeading">{l s="Quel est votre code postal" mod='tunnelventeabies'}</span>
        <div class="npa-wrap">
            <div class="npa-wrap-inputs">
                <input type="hidden" name="npa" class="npa_input-hidden" />
{*                <input type="text" data-input="1" id="npaInput1" class="single-npa-input" placeholder="#" maxlength="1" />*}
{*                <input type="text" data-input="2" id="npaInput2" class="single-npa-input" placeholder="#" maxlength="1" />*}
{*                <input type="text" data-input="3" id="npaInput3" class="single-npa-input" placeholder="#" maxlength="1" />*}
{*                <input type="text" data-input="4" id="npaInput4" class="single-npa-input" placeholder="#" maxlength="1" />*}
                <input type="text"  id="npaInput" class="single-npa-input" placeholder="####" maxlength="4" />
            </div>
            <button type="submit">{l s="Valider" mod='tunnelventeabies'}</button>
        </div>
        <br/>

        <div class="btns_next_prev">
            <button type="submit" class="next">next</button>
        </div>
    </form>
{else}
    <div class="container">
        <h4 style="color: red;">
            {l s="La réservation de votre Ecosapin sera possible dès le vendredi 18 octobre. A bientôt" mod='tunnelventeabies'}
        </h4>
    </div>
{/if}
{literal}
    <script type="text/javascript">
        $(function ($) {
            //current
            $('.container_npa').removeClass('hidden');
            $('.container_type').addClass('hidden');


            // $('.single-npa-input').on('input', function() {
            //     var inputNumber = parseInt($(this).attr('data-input')),
            //         nextInput = inputNumber+1;
            //
            //     if (nextInput <= 4) {
            //         $('#npaInput' + nextInput).focus();
            //     }
            // });

            $('form.form_npa').submit(function (event) {
                event.preventDefault();
                var npa = $('.single-npa-input').val();
                // $('.single-npa-input').each(function() {
                //     if ($(this).val()) {
                //         npa += $(this).val();
                //     }
                // });

                if (npa == "" || npa.length != 4 || !$.isNumeric(npa)) {
                    alert("invalide NPA");
                    return false;
                }

                $('.npa_input-hidden').val(npa);
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