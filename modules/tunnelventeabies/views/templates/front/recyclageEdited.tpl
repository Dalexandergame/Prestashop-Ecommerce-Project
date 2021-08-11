<div class="steps-header-mobile">
    <div class="container-steps-mobile">
        <div class="step-name">
            <span>Etape 4</span>/5
        </div>
        <div class="step-name">
            {if $lang_iso == 'fr' }
                Livraison et recyclage
            {elseif $lang_iso == 'en'}
                Pick-up and recycling
            {elseif $lang_iso == 'de'}
                Abholung und Recycling
            {/if}
        </div>
    </div>
</div>
<form action="{$base_url}module/tunnelventeabies/finale" id="form_recyclage"
      method="post">
    <div class="btns_next_prev mobile-respo">
        <button type="button" class="prev">prev</button>
        {if $lang_iso == 'fr' }
            <span class="text-prev">{l s="Précédent"}</span>
        {elseif $lang_iso == 'en'}
            <span class="text-prev">{l s="Previous"}</span>
        {elseif $lang_iso == 'de'}
            <span class="text-prev">{l s="Zurück"}</span>
        {/if}
        <button type="submit" class="next">next</button>
        {if $lang_iso == 'fr' }
            <span class="next text-next">{l s="Suivant"}</span>
        {elseif $lang_iso == 'en'}
            <span class="next text-next">{l s="Next"}</span>
        {elseif $lang_iso == 'de'}
            <span class="next text-next">{l s="Weiter"}</span>
        {/if}
    </div>
      <input type="hidden" name="recyclage" id="selectedRecyclageId" value="0" />
    {if $lang_iso == 'fr' }
        <span class="tunnelVenteHeading font-serif-title">{l s="Choisissez le type de recyclage" }</span>
    {elseif $lang_iso == 'en'}
        <span class="tunnelVenteHeading font-serif-title">{l s="Select the recycling option" }</span>
    {elseif $lang_iso == 'de'}
        <span class="tunnelVenteHeading font-serif-title">{l s="Wählen Sie die Recycling-Art" }</span>
    {/if}
    <div class="row options-step container-recyclage">
        <div class="col-md-4">
            <div data-id="{$product.id}"
                    data-price="{$product.price}"
                    data-name="{$product.description_short}" class="option-attribute-container">
                <div class="attribute-img-container"
                     style="background-image:url('{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}modules/tunnelventeabies/images/recyclage/abies_recycle.JPG');
                             background-position: center;background-size: contain;background-color: #193128;background-repeat: no-repeat;">
                    <div class="attribute-hover-container">
                        <button type="button" class="tunnel-recycle-action tunnel-add-btn">Add</button>
                    </div>
                </div>
{*                <div class="attribute-desc-recycle-container">{$product.description_short|strip_tags} (+ {$product.price}{$currency->sign})</div>*}
                <div class="attribute-desc-recycle-container">{if $lang_iso == 'fr' }
                        My Abies récupère mon sapin (+ 30 CHF)
                    {elseif $lang_iso == 'en'}
                        My Abies picks my tree up (+ 30 CHF)
                    {elseif $lang_iso == 'de'}
                        My Abies holt meinen Weihnachtsbaum ab (+ 30 CHF)
                    {/if}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div data-id="0" data-price="0.00" data-name="{l s="Je m'occupe de le recycler moi même" mod='tunnelventeabies'}" class="option-attribute-container">
                <div class="attribute-img-container"
                     style="background-image:url('{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}modules/tunnelventeabies/images/recyclage/manual_recycle.JPG');
                             background-position: center;background-size: contain;background-color: #193128;background-repeat: no-repeat;">
                    <div class="attribute-hover-container">
                        <button type="button" class="tunnel-recycle-action tunnel-add-btn">Add</button>
                    </div>
                </div>
                {if $lang_iso == 'fr' }
                    <div class="attribute-desc-recycle-container">Je m'occupe de le recycler moi même</div>
                {elseif $lang_iso == 'en'}
                    <div class="attribute-desc-recycle-container">I will recycle my tree my self</div>
                {elseif $lang_iso == 'de'}
                    <div class="attribute-desc-recycle-container">Ich werde meinen Baum selbst recyceln</div>
                {/if}

            </div>
        </div>
        <div class="col-md-4">
            <div class="overview-tunnel-container"></div>
        </div>
    </div>

    <div class="btns_next_prev desktop">
        <button type="button" class="prev">prev</button>
        {if $lang_iso == 'fr' }
            <span class="text-prev">{l s="Précédent"}</span>
        {elseif $lang_iso == 'en'}
            <span class="text-prev">{l s="Previous"}</span>
        {elseif $lang_iso == 'de'}
            <span class="text-prev">{l s="Zurück"}</span>
        {/if}
        <button type="submit" class="next">next</button>
        {if $lang_iso == 'fr' }
            <span class="next text-next">{l s="Suivant"}</span>
        {elseif $lang_iso == 'en'}
            <span class="next text-next">{l s="Next"}</span>
        {elseif $lang_iso == 'de'}
            <span class="next text-next">{l s="Weiter"}</span>
        {/if}
    </div>
    <div class="loading"></div>
</form>

{literal}
    <script type="text/javascript">
        $(function ($) {
            var recyclage_backtunnelvente = "{/literal}{$base_url}{literal}module/tunnelventeabies/pied?back=1",
                recyclageId = "{/literal}{if !empty($last_id_recyclage_checked)}{$last_id_recyclage_checked}{else}0{/if}{literal}";

            calPrice = $('.priceCalcContainer').data('calPrice');

            if (recyclageId > 0) {
                $('.option-attribute-container').each(function() {
                    var dataRecyclage = $(this).attr('data-id'),
                        dataRecyclagePrice = $(this).attr('data-price');

                    if (dataRecyclage == recyclageId) {
                        calPrice.setPriceRecyclage(dataRecyclagePrice, dataRecyclage, "{/literal}{l s="Recyclage & Récupération" mod='tunnelventeabies'}{literal}", false);
                        $('#selectedRecyclageId').val(dataRecyclage);

                        $(this).addClass('active');
                        $(this).find('.tunnel-recycle-action').removeClass('tunnel-add-btn').addClass('tunnel-remove-btn');
                    }
                });
            } else {
                calPrice.cal(true);
            }

            $('.tunnel-recycle-action').on('click', function(e) {
                e.preventDefault();

                var recyclePrice = $(this).closest('.option-attribute-container').attr('data-price'),
                    recycleId = $(this).closest('.option-attribute-container').attr('data-id'),
                    toRemove = false;

                $('.option-attribute-container').each(function() {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');

                        if (recycleId == $(this).attr('data-id')) {
                            toRemove = true;
                        }
                    }
                });
                $('.tunnel-recycle-action').each(function() {
                    if ($(this).hasClass('tunnel-remove-btn')) {
                        $(this).removeClass('tunnel-remove-btn').addClass('tunnel-add-btn');
                    }
                });

                if ($(this).hasClass('tunnel-add-btn') && !toRemove) {
                    $('#selectedRecyclageId').val(recycleId);
                    recyclageId = recycleId;

                    calPrice.setPriceRecyclage(recyclePrice, recycleId, "{/literal}{l s="Recyclage & Récupération" mod='tunnelventeabies'}{literal}", false);
                    $(this).removeClass('tunnel-add-btn').addClass('tunnel-remove-btn');
                    $(this).closest('.option-attribute-container').addClass('active');
                } else if(toRemove) {
                    calPrice.unsetPriceRecyclage(recyclePrice, "{/literal}{l s="Recyclage & Récupération" mod='tunnelventeabies'}{literal}", false);
                }
            });

            $('form#form_recyclage .prev').click(function (event) {
                $(".container_recyclage").addClass('hidden');

                var $me = $(this);
                var classe = 'isactive';

                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);
                    try {
                        $.ajax({
                            type: 'GET',
                            url: recyclage_backtunnelvente,
                            data: 'ajax=1&back=1&recyclage=' + recyclageId,
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
                            data: 'ajax=1&' + $me.serialize(),
                            dataType: 'json',
                            success: function (json) {
                                if (json.hasError) {
                                    return true;

                                    $.each(json.errors, function (k, v) {
                                        showError(v);
                                    });
                                } else {
                                    // if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
                                    //     event.preventDefault();
                                    //
                                    //     var commandeUrl = link;
                                    //     document.location.href = commandeUrl;
                                    //
                                    //     return false;
                                    // }

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
        });
    </script>
{/literal}
