<form action="{$urls.base_url}module/tunnelventeabies/recyclage" id="form_pied"
      method="post">
    <span class="tunnelVenteHeading">{l s="Choisissez le type de pied" mod='tunnelventeabies'}</span>
    <div class="row options-step">
        <div class="col-md-3">
            <div class="option-attribute-container">
                <div class="attribute-img-container" style="background-color:white;">
                    <span class="attribute-img-description">Sans Pied</span>
                </div>
                <div class="attribute-price-container"><span>Dès</span> 0.00 {$currency.sign}</div>
            </div>
        </div>
        {foreach from=$result item=pied}
            <div class="col-md-3">
                <div class="option-attribute-container">
                    <div class="attribute-img-container"
                         style="background-image:url({$link->getImageLink($pied.link_rewrite, $pied.id_product|cat:'-'|cat:$pied.id_image, 'large_default')|escape:'html':'UTF-8'});
                                 background-position: center;background-size: cover;">
                        <span class="attribute-img-description">{$pied.name}</span>
                    </div>
                    <div class="attribute-price-container"><span>Dès</span> {$pied.price_ttc} {$currency.sign}</div>
                </div>
            </div>
        {/foreach}
    </div>

    <div class="btns_next_prev">
        <button type="button" class="prev">prev</button>
        <button type="submit" class="next">next</button>
    </div>
    <div class="loading"></div>
</form>

<script type="text/javascript">
    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelventeabies/taille?back=1";
</script>

{literal}
    <script type="text/javascript">
        $(function ($) {
            //current
            $('.container_pied').removeClass('hidden');
            //previous
            $('#blockProduct').addClass('hidden');
            $('.container_taille_pot').addClass('hidden');
            //next
            $('.container_recyclage').addClass('hidden');

            $('form#form_pied .prev').click(function (event) {
                var $me = $(this), classe = 'isactive';
                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);
                    try {
                        $.ajax({
                            type: 'GET',
                            url: baseurl_tunnelvente,
                            data: 'ajax=1&back=1',
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

            $('form#form_pied').submit(function (event) {
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

            $.each($("#form_pied input:radio.radio_pied"), function () {
                $(this).prev().css('background-color', $(this).data('bg'));
            });

            $("#form_pied input:radio.radio_pied").click(function () {
                var $me = $(this), $parent = $me.parents('li'), calPrice;

                $('form#form_pied label').removeClass("checked");
                $parent.find('label').addClass('checked');

                /* pied block right*/
                if ($parent.find('img').length) {
                    $('.thirdCol .container_pied img').attr('src', $parent.find('img').attr('src')).show();
                    $('.thirdCol .textpiedpromo').show();
                    $('.thirdCol .text_p_contractuelle').show();
                } else {
                    $('.thirdCol .container_pied img').hide();
                    $('.thirdCol .textpiedpromo,.thirdCol .text_p_contractuelle').hide();
                }

                calPrice = $('.thirdCol').data('calPrice');
                calPrice.setPricePied($me.data('price'), true);

                if (parseInt($me.val()) == 0) {
                    $('input#not_pied').attr('checked', true);
                }

                var element = $('#pied_' + $me.data('idattribut'));

                $('.description, .description div > div').hide();

                if (element.length) {
                    element.show();
                    $('.description').show();
                }

                var img = $me.data('image');
                var html = '<img src="' + img + '" width="100%" height="100%" />';

                $(".container_pied").html(html)
            });

            $.each($('form#form_pied  input:checked').parents('span'), function () {
                $(this).find('input:radio').click();
            });

            if (!$('form#form_pied  input:checked').length && $('form#form_pied input:radio').length) {
                $('form#form_pied  input:radio:eq(0)').click();
            }
        });
    </script>
{/literal}

<style type="text/css">
    .description div > div {
        display: none;
    }
</style>
