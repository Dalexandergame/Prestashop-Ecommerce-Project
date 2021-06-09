<form action="{$urls.base_url}module/tunnelventeabies/{if $isSapinSwiss}pied{else}recyclage{/if}"
      id="form_taille" method="post">
    <h4>{l s="Choose fir size" mod='tunnelventeabies'}</h4>
    <ul>
        {foreach from=$tailles item=taille}
            <li data-id="{$taille.id}" {if $taille.quantity < 1} class="disabled" {/if}
                data-src="{$urls.base_url}modules/tunnelvente/images/tailles/{$taille.name|replace:" ":"_"|replace:"/":"x"}{if !$taille.enpot}-coupe{/if}.png">
                <input id="taille_{$taille.id}" type="radio" name="taille" value="{$taille.id}"
                        {if $taille.id == $id_attribute_taille } checked {/if}
                        {if $taille.quantity < 1} disabled {/if}
                />
                <label data-image="{$taille.image}.png" data-price="{$taille.price}"
                       for="taille_{$taille.id}">{$taille.name}
                    <span class="taille_taille">
                    {if $taille.enpot == " en pot"}
                        {l s=" en pot" mod='tunnelventeabies'}
                    {else}
                        {l s=" coupé avec pied" mod='tunnelventeabies'}
                    {/if}
                    </span>
                    {if $taille.quantity < 1}
                        <span class="taille_taille"
                              style="background-color: red">{l s="Rupture de stock" mod='tunnelventeabies'}</span>
                    {/if}
                </label>
            </li>
        {/foreach}
    </ul>
    {if count($tailles) }
        <div class="icon-tunnel">
            <div class="cercle_taille cercle"></div>
        </div>
    {/if}
    <div class="clear"></div>
    <div class="description">
        <div class="msg_info">
            {if $typetpl == "ecosapin"}
                <h4 class="prev">{l s="Bigger? Choose Swiss Fir" mod='tunnelventeabies'}</h4>
            {elseif $typetpl == "sapinsuisse"}
                <script>
                    $(function ($) {
                        $('.cercle_taille').addClass("sap-coupe");
                    });
                </script>
                <a title="Contactez-nous" target="_blank"
                   href="{$urls.base_url}fr/contactez-nous">
                    <h4>{l s="Even bigger, contact us" mod='tunnelventeabies'}</h4></a>
            {/if}
        </div>
    </div>

    <div class="btns_next_prev">
        <button type="button" class="prev">prev</button>

        {if count($tailles) }
            <button type="submit" class="next">next</button>
        {/if}
    </div>
    <div class="loading"></div>
</form>

<script type="text/javascript">
    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelventeabies/type";
    var baseurl = "{$urls.base_url}img/cms/tunnel_tailles/";

    {if count($tailles) }
    $('.container_taille_pot img').removeClass("hidden");
    {else}
    $('.container_taille_pot img').addClass("hidden");
    {/if}
</script>
{literal}
    <script type="text/javascript">
        $(function ($) {
            //current
            $('.container_taille_pot').removeClass('hidden');
            //previous
            $('.container_type').addClass('hidden');
            //next
            $('.container_pied').addClass('hidden');
            //next
            $('.container_recyclage').addClass('hidden');

            $('form#form_taille .prev').click(function (event) {
                $('.prix').addClass('hidden');
                var $me = $(this), classe = 'isactive';

                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);
                    try {
                        $.ajax({
                            type: 'GET',
                            url: baseurl_tunnelvente,
                            data: 'ajax=1&back=2',
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
                            }
                        });
                    } catch (e) {
                        $me.removeClass(classe);
                    }
                }
            });

            $('form#form_taille').submit(function (event) {
                event.preventDefault();
                var $me = $(this), classe = 'isactive';
                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);

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
                            }
                        });
                    } catch (e) {
                        $me.removeClass(classe);
                    }
                }
            });

            $('form#form_taille li:not(.disabled) label').click(function () {
                var $me = $(this);
                $('form#form_taille li label').removeClass("checked");
                $(this).addClass("checked");
                $('.thirdCol .container_taille img').attr('src', $(this).parents('li').data('src')).show();
                /* price recyclage */
                $('.container_taille_pot img').attr('src', baseurl + $me.data('image'));
                calPrice = $('.thirdCol').data('calPrice');
                calPrice.setPriceSapin($me.data('price'), true);
            });

            $.each($('form#form_taille li input:checked').parents('li'), function () {
                $(this).find('label').click();
            });

            if (!$('form#form_taille li input:checked').length && $('form#form_taille li:not(.disabled) label:eq(0)').length) {
                $('form#form_taille li:not(.disabled) label:eq(0)').click();
            }

            $('.prix').removeClass('hidden');
        });
    </script>
{/literal}
