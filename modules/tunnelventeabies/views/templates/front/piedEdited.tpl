<div class="steps-header-mobile">
    <div class="container-steps-mobile">
        <div class="step-name">
            <span>Etape 3</span>/5
        </div>
        <div class="step-name">
            {if $language.iso_code == 'fr' }
                Type de pied
            {elseif $language.iso_code == 'en'}
                Base type
            {elseif $language.iso_code == 'de'}
                Ständerarten
            {/if}
        </div>
    </div>
</div>
<form action="{$urls.base_url}module/tunnelventeabies/recyclage" id="form_pied"
      method="post">
    <div class="btns_next_prev mobile-respo">
        <button type="button" class="prev">prev</button>
        {if $language.iso_code == 'fr' }
            <span class="text-prev">{l s="Précédent"}</span>
        {elseif $language.iso_code == 'en'}
            <span class="text-prev">{l s="Previous"}</span>
        {elseif $language.iso_code == 'de'}
            <span class="text-prev">{l s="Zurück"}</span>
        {/if}
        <button type="submit" class="next">next</button>
        {if $language.iso_code == 'fr' }
            <span class="next text-next">{l s="Suivant"}</span>
        {elseif $language.iso_code == 'en'}
            <span class="next text-next">{l s="Next"}</span>
        {elseif $language.iso_code == 'de'}
            <span class="next text-next">{l s="Weiter"}</span>
        {/if}
    </div>
    <input type="hidden" name="pied" id="selectedPiedId" value="0" />
    {if $language.iso_code == 'fr' }
        <span class="tunnelVenteHeading font-serif-title">{l s="Choisissez le type de pied"}</span>
    {elseif $language.iso_code == 'en'}
        <span class="tunnelVenteHeading font-serif-title">{l s="Select the type of base"}</span>
    {elseif $language.iso_code == 'de'}
        <span class="tunnelVenteHeading font-serif-title">{l s="Wählen Sie die Ständerart"}</span>
    {/if}
    <div class="row options-step">
        <div class="col-md-3">
            <div data-id="0" data-price="0.00"
                 data-name="{if $language.iso_code == 'fr' }Sans Pied{elseif $language.iso_code == 'en'}No Base{elseif $language.iso_code == 'de'}Ohne Ständer{/if}"
                 class="option-attribute-container">
                <div class="attribute-img-container"
                     style="background:url({$urls.img_url}Tronc-simple1.jpg); background-position: center;background-size: 100% 350px;background-repeat: no-repeat;">
                    <div class="attribute-hover-container">
                        <button type="button" class="tunnel-pied-action tunnel-add-btn">Add</button>
                    </div>
                    {if $language.iso_code == 'fr' }
                        <span class="attribute-img-description">Sans Pied</span>
                    {elseif $language.iso_code == 'en'}
                        <span class="attribute-img-description">No base</span>
                    {elseif $language.iso_code == 'de'}
                        <span class="attribute-img-description">Ohne Ständer</span>
                    {/if}
                </div>
                <div class="attribute-price-container">
                    {if $language.iso_code == 'fr' }
                        <span>Dès</span>
                    {elseif $language.iso_code == 'en'}
                        <span>From</span>
                    {elseif $language.iso_code == 'de'}
                        <span>Ab</span>
                    {/if}
                    0.00 {$currency.sign}</div>
            </div>
        </div>
        {foreach from=$result item=pied}
            <div class="col-md-3">
                <div data-id="{$pied.id_product_attribute}" data-price="{$pied.price_ttc}" data-name="{$pied.name}" class="option-attribute-container">
                    <div class="attribute-img-container" style="background-image:url({$link->getImageLink($pied.link_rewrite, $pied.id_product|cat:'-'|cat:$pied.id_image, 'large_default')|escape:'html':'UTF-8'});
                            background-position: center;background-size: 100% 350px;background-repeat: no-repeat;">
                        <div class="attribute-hover-container">
                            <button type="button" class="tunnel-pied-action tunnel-add-btn">Add</button>
                        </div>
                        <span class="attribute-img-description">{$pied.name}</span>
                    </div>
                    <div class="attribute-price-container">
                        {if $language.iso_code == 'fr' }
                            <span>Dès</span>
                        {elseif $language.iso_code == 'en'}
                            <span>From</span>
                        {elseif $language.iso_code == 'de'}
                            <span>Ab</span>
                        {/if}
                        {$pied.price_ttc} {$currency.sign}</div>
                </div>
            </div>
        {/foreach}
        <div class="col-md-3 price-container desktop">
            <div class="overview-tunnel-container"></div>
        </div>
    </div>
    <div class="respo-mobile dots-tunnel">
        <span class="dot active" id="dot1"></span>
        <span class="dot" id="dot2"></span>
        <span class="dot" id="dot3"></span>
    </div>
    <div class="col-md-3 price-container respo-mobile">
        <div class="overview-tunnel-container"></div>
    </div>


    <div class="btns_next_prev desktop">
        <button type="button" class="prev">prev</button>
        {if $language.iso_code == 'fr' }
            <span class="text-prev">{l s="Précédent"}</span>
        {elseif $language.iso_code == 'en'}
            <span class="text-prev">{l s="Previous"}</span>
        {elseif $language.iso_code == 'de'}
            <span class="text-prev">{l s="Zurück"}</span>
        {/if}
        <button type="submit" class="next">next</button>
        {if $language.iso_code == 'fr' }
            <span class="next text-next">{l s="Suivant"}</span>
        {elseif $language.iso_code == 'en'}
            <span class="next text-next">{l s="Next"}</span>
        {elseif $language.iso_code == 'de'}
            <span class="next text-next">{l s="Weiter"}</span>
        {/if}
    </div>
    <div class="loading"></div>
</form>

<script type="text/javascript">
    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelventeabies/taille?back=1";
    {if !empty($id_product_pied)}
        var id_product_pied = "{$id_product_pied}";
    {else}
        var id_product_pied = 0;
    {/if}
</script>

{literal}
    <script type="text/javascript">
        $(function ($) {
            /* partie mobile dots*/
            $('form#form_pied .row.options-step').on('touchend',function () {
                var scroll_left_width = $('form#form_pied .row.options-step').scrollLeft();
                var div_width =$('form#form_pied .row.options-step').width();
                if (scroll_left_width < div_width - 10) {
                    $('#dot1').addClass('active')
                    $('#dot2').removeClass('active');
                    $('#dot3').removeClass('active');
                } else if (scroll_left_width >= div_width -10 && scroll_left_width < (div_width*2 -10) ) {
                    $('#dot2').addClass('active')
                    $('#dot1').removeClass('active');
                    $('#dot3').removeClass('active');
                } else if (  scroll_left_width >= (div_width * 2 - 10) ) {
                    $('#dot3').addClass('active')
                    $('#dot2').removeClass('active');
                    $('#dot1').removeClass('active');
                }
            });
            $('#dot1').on('click', function (e) {
                e.preventDefault();
                $('form#form_pied .row.options-step').scrollLeft(0);
                $('#dot1').addClass('active')
                $('#dot2').removeClass('active');
                $('#dot3').removeClass('active');
            });
            $('#dot2').on('click', function (e) {
                e.preventDefault();
                $('form#form_pied .row.options-step').scrollLeft($('form#form_pied .row.options-step').width());
                $('#dot2').addClass('active')
                $('#dot1').removeClass('active');
                $('#dot3').removeClass('active');
            });
            $('#dot3').on('click', function (e) {
                e.preventDefault();
                $('form#form_pied .row.options-step').scrollLeft($('form#form_pied .row.options-step').width()*2);
                $('#dot3').addClass('active')
                $('#dot2').removeClass('active');
                $('#dot1').removeClass('active');
            });
            /* dots */
            
            var isSelected = true;
            calPrice = $('.priceCalcContainer').data('calPrice');

            $('.option-attribute-container').each(function() {
                var dataPied = $(this).attr('data-id'),
                    dataPiedPrice = $(this).attr('data-price'),
                    dataPiedName = $(this).attr('data-name');

                if (dataPied == id_product_pied) {
                    calPrice.setPricePied(dataPiedPrice, dataPiedName, false);
                    $('#selectedPiedId').val(dataPied);

                    $(this).addClass('active');
                    $(this).find('.tunnel-pied-action').removeClass('tunnel-add-btn').addClass('tunnel-remove-btn');
                }
            });
            if (id_product_pied == 0) {
                calPrice.cal(true);
            }

            $('.tunnel-pied-action').on('click', function(e) {
                e.preventDefault();

                var piedPrice = $(this).closest('.option-attribute-container').attr('data-price'),
                    piedName = $(this).closest('.option-attribute-container').attr('data-name'),
                    piedId = $(this).closest('.option-attribute-container').attr('data-id'),
                    toRemove = false;

                $('.option-attribute-container').each(function() {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');

                        if (piedId == $(this).attr('data-id')) {
                            toRemove = true;
                        }
                    }
                });
                $('.tunnel-pied-action').each(function() {
                    if ($(this).hasClass('tunnel-remove-btn')) {
                        $(this).removeClass('tunnel-remove-btn').addClass('tunnel-add-btn');
                    }
                });

                if ($(this).hasClass('tunnel-add-btn') && !toRemove) {
                    $('#selectedPiedId').val(piedId);
                    id_product_pied = piedId;

                    calPrice.setPricePied(piedPrice, piedName, false);
                    $(this).removeClass('tunnel-add-btn').addClass('tunnel-remove-btn');
                    $(this).closest('.option-attribute-container').addClass('active');
                    isSelected = true;
                } else if(toRemove) {
                    calPrice.unsetPricePied(piedPrice, piedName, false);
                    isSelected = false;
                }
            });

            $('form#form_pied .prev').click(function (event) {
                var $me = $(this), classe = 'isactive';
                if (!$me.hasClass(classe)) {
                    $me.addClass(classe);
                    try {
                        $.ajax({
                            type: 'GET',
                            url: baseurl_tunnelvente,
                            data: 'ajax=1&back=1&pied=' + id_product_pied,
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

                if (!isSelected) {
                    showError('{/literal}{l s='Veuillez selectionner au moins une option!' mod='tunnelventeabies'}{literal}');
                    return;
                }

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
        });
    </script>
{/literal}
