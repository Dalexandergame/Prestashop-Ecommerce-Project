<form action="{$base_url}/module/tunnelvente/pot" id="form_boule" method="post">
    <h4>{l s="Choisissez le type de décoration" mod='tunnelvente'}</h4>
    <div class="left-step1">
        <div class="list_boule">
            {foreach from=$result item=boule}
                <span class="boule boule_{$boule.id_product_attribute}">
                <input style="display: none;" type="radio" name="boule" class="radio_boule"
                       value="{$boule.id_product_attribute}" {if isset($id_product_boule) and $boule.id_product_attribute == $id_product_boule } checked="" {/if} title="{$boule.name}"
                       data-idattribut="{$boule.id_attribute}" data-price="{$boule.price_ttc}" data-bg="{$boule.color}"
                       data-image="//{$link->getImageLink($boule.link_rewrite, $boule.id_product|cat:'-'|cat:$boule.id_image, 'large_default')|escape:'html':'UTF-8'}"
                />
                {$boule.name}
            </span>
                {foreachelse}
                <span class="boule">
                <input style="display: none;" type="radio" name="boule" class="radio_boule" value="0" checked
                       data-price="0"/>
                {l s="Aucune décoration disponible"  mod='tunnelvente'}
            </span>
                <img class="eco_img" style="display: none;"
                     src="{$base_url}/module/tunnelvente/views/img/bg-default-white.png"/>
            {/foreach}
        </div>
        {if count($result)}
            <ul>
                <li data-id="0">
                    <input type="radio" data-price="0" name="boule" value="0" id="boule_0"/>
                    <label for="boule_0">{l s="Non merci j'ai mes propres boule" mod='tunnelvente'}</label>
                    <img class="eco_img" style="display: none;"
                         src="{$base_url}/module/tunnelvente/views/img/bg-default-white.png"/>
                </li>
            </ul>
        {/if}
    </div>

    <div class="icon-tunnel">
        <div class="cercle_ch_boule cercle"></div>
        {*<h2>{l s="Choisissez le type de décoration" mod='tunnelvente'}</h2>*}
    </div>
    <div class="clear"></div>
    <div class="description">
        <div>
            {$product->description nofilter}
        </div>
    </div>
    <div class="btns_next_prev">
        <button type="button" class="prev">prev</button>
        <button type="submit" class="next">next</button>
    </div>
    <div class="loading"></div>
</form>

<script type="text/javascript">
    var baseurl_tunnelvente = "{$base_url}/module/tunnelvente/recyclage?back=1";
</script>

{literal}
    <script type="text/javascript">
        $(function ($) {
            //current
            $('.container_boule').removeClass('hidden');
            //previous
            $('.container_recyclage').addClass('hidden');
            //next
            $('.container_pot').addClass('hidden');

            $('form#form_boule .prev').click(function (event) {
                var $me = $(this), classe = 'isactive';

                $(".container_recyclage").css('height', 'auto');
                $(".container_recyclage").css('overflow', 'auto');
                $(".container_recyclage").removeClass('hidden');
                $(".container_boule").addClass('hidden');

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

            $('form#form_boule').submit(function (event) {
                event.preventDefault();

                var $me = $(this), classe = 'isactive';

                if (!$me.hasClass(classe)) {
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
                                }
                            }
                        });
                    } catch (e) {
                    }
                }
            });

            $('form#form_boule .list_boule .boule').click(function () {
                var $me = $(this), calPrice;

                $('form#form_boule .list_boule .boule').not($me).removeClass("checked");
                $me.find('input').attr('checked', true);
                $(this).addClass("checked");

                if ($me.find('img.eco_img').length) {
                    $('.thirdCol .container_boule img').attr('src', $me.find('img.eco_img').attr('src')).show();
                    $('.thirdCol .textboutpromo').show();
                    $('.thirdCol .text_p_contractuelle').show();
                } else {
                    $('.thirdCol .container_boule img,.thirdCol .text_p_contractuelle').hide();
                }

                /* no comment! */
                $('form#form_boule li label').removeClass("checked");

                /* price boule */
                calPrice = $('.thirdCol').data('calPrice');

                /* no comment! */
                calPrice.setPriceBoule($me.find('input').data('price'), true);

                /* show/hide description déco  */
                $('.description div > div').hide();
                $('#deco_' + $me.find('input').data('idattribut')).show();

                var img = $me.find('input').data('image');
                var html = '<img src="' + img + '" width="100%" height="100%" />';

                console.log(img);
                $(".container_boule").html(html)
            });

            $('form#form_boule li label').click(function () {
                var $me = $(this), $parent = $me.parents("li"), calPrice;

                $('form#form_boule .list_boule .boule').removeClass("checked");
                $(this).addClass("checked");
                $(this).prev().prop('checked', true);

                /* price boule */
                calPrice = $('.thirdCol').data('calPrice');

                calPrice.setPriceBoule($parent.find('input').data('price'), true);
                $('.thirdCol .container_boule img,.thirdCol .text_p_contractuelle').hide();
                $('.thirdCol .textboutpromo').hide();

                /* hide description déco  */
                $('.description, .description div > div').hide();
            });
            $('form#form_boule input:radio:eq(0)').click();
        });
    </script>
{/literal}

<style type="text/css">
    .description div > div {
        display: none;
    }
</style>
