<form action="{$urls.base_url}module/tunnelvente/boule" id="form_recyclage"
      method="post">
    <h4>{l s="Choisissez le type de recyclage" d='Modules.Tunnelvente.Recyclage'}</h4>
    <ul>
        <li data-id="{$product.id}">
            <input type="radio" name="recyclage" data-price="{$product.price}" value="{$product.id}"
                   id="recyclage_{$product.id}" {if isset($last_id_recyclage_checked) and $product.id == $last_id_recyclage_checked} checked="checked" {/if}/>
            <label for="recyclage_{$product.id}">{$product.description_short nofilter}</label>
            {if !empty($image_recyclage)}
                <img style="display: none;"
                     src="{$urls.base_url}modules/tunnelvente/images/recyclage/{$image_recyclage}"/>
            {/if}
        </li>
        <li data-id="0">
            <input type="radio" name="recyclage" data-sapinPrice="" data-price="0" value="0"
                   id="recyclage_0" {if isset($last_id_recyclage_checked) and 0 == $last_id_recyclage_checked} checked="checked" {/if} />
            <label for="recyclage_0">{l s="Je m'occupe de le recycler moi même" d='Modules.Tunnelvente.Recyclage'}</label>
        </li>
    </ul>
    <div class="icon-tunnel">
        <div class="cercle_recyclage cercle"></div>
    </div>
    <div class="clear"></div>
    <div class="description">

        <div class="description_{$product.id}" style="display: none">
            {$product.description nofilter}
        </div>
        <div class="description_0" style="display: none">
            {l s="Texte Je m'occupe de le recycler moi même" d='Modules.Tunnelvente.Recyclage'}
        </div>
    </div>
    <div class="btns_next_prev">
        <button type="button" class="prev">prev</button>
        <button type="submit" class="next">next</button>
    </div>
    <div class="loading"></div>
</form>

<script type="text/javascript">
    {*var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/taille?back=2";*}
    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/{if $isSapinSwiss}pied{else}taille{/if}?back=1";
    var partnerImg = "{$urls.base_url}modules/ecosapinpartners/uploads/{$resume.transporteur_img}";
    var link = "{$link->getPageLink($order_process, true)|escape:'html':'UTF-8'}";

    $(".container_recyclage").removeClass('hidden');
    $(".container_recyclage #transporteur").html('{$resume.transporteur}');
    $(".container_recyclage #type").html('{$resume.type}');
    $(".container_recyclage #taille").html('{$resume.taille}');
    $(".container_recyclage #prix").html('{$resume.prix}');
    $(".container_recyclage .left_Type img").attr('src', partnerImg);
</script>
{literal}
    <script type="text/javascript">
        $(function ($) {
            //current
            $('.container_recyclage').removeClass('hidden');
            //previous
            $('.container_pied').addClass('hidden');
            $('.container_taille_pot').addClass('hidden');
            //next
            $('.container_boule').addClass('hidden');

            $('form#form_recyclage .prev').click(function (event) {
                $(".container_recyclage").addClass('hidden');

                var $me = $(this);
                var classe = 'isactive';

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

            $('form#form_recyclage').submit(function (event) {
                event.preventDefault();
                $(".container_recyclage").addClass('hidden');
                var $me = $(this), classe = 'isactive';
                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);

                    try {
                        $.ajax({
                            type: 'POST',
                            url: $me.attr("action"),
                            data: 'ajax=1&' + $me.serialize() + (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ? "&mobile=1" : ""),
                            dataType: 'json',
                            success: function (json) {
                                if (json.hasError) {
                                    return true;

                                    $.each(json.errors, function (k, v) {
                                        showError(v);
                                    });
                                } else {
                                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
                                        event.preventDefault();

                                        var commandeUrl = link;
                                        document.location.href = commandeUrl;

                                        return false;
                                    }

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

            $('form#form_recyclage input:radio').click(function () {
                var $me = $(this), $parent = $me.parents("li"), id = $me.parents("li").data('id'), calPrice;
                $('form#form_recyclage .description').find("> div").hide()
                    .end()
                    .find(".description_" + id).show();

                /* image block right*/
                if ($parent.find('img').length) {
                    $('.thirdCol .container_recyclage .image_right > img').attr('src', $parent.find('img').attr('src')).show();
                    $('.thirdCol .text_p_contractuelle').show();
                } else {
                    $('.thirdCol .container_recyclage > img,.thirdCol .text_p_contractuelle').hide();
                }

                /* label */
                $('form#form_recyclage li label').removeClass("checked");
                $parent.find('label').addClass("checked");

                /* price recyclage */
                calPrice = $('.thirdCol').data('calPrice');
                calPrice.setPriceRecyclage($me.data('price'), $me.val(), true);
            });

            $.each($('form#form_recyclage li input:checked').parents('li'), function () {
                $(this).find('input').click();
            });
        });
    </script>
{/literal}
