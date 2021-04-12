{if isset($skip_pot) && $skip_pot}

<script type="text/javascript">
    $.ajax({
        url: "{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}module/tunnelvente/{if isset($back) && $back}boule?back=1{else}autresapin{/if}",
        type: "post",
        data: 'ajax=1&pot=0',
        dataType: 'json',
        success: function(json) {
            if(json.hasError){
                $.each(json.errors,function(k,v){
                    showError(v);
                });
            }else{
                $('#resp_content').html(json.html);
                $('#my_errors').empty();
                ShowHideStep(json.numStep);
            }

        }
    });
</script>
{else}
    <form action="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}module/tunnelvente/autresapin" id="form_pot"
          method="post">
<h4>{l s="Choisissez le cache pot" mod='tunnelvente'}</h4>
    <div class="list_pot">
        {foreach from=$result item=pot}
            <span>
                <input type="radio" name="pot" class="radio_pot" value="{$pot.id_product_attribute}" title="{$pot.name}" onclick="displayImage{$pot.id_product_attribute}()"
                       {if isset($last_id_product_pot_checked) and $pot.id_product_attribute == $last_id_product_pot_checked} checked="" {/if}
                        data-price="{$pot.price_ttc}" data-bg="{$pot.color}"/>
                   <script>
                        function displayImage{$pot.id_product_attribute}(img){
                            var img = "{$link->getImageLink($pot.link_rewrite, $pot.id_product|cat:'-'|cat:$pot.id_image, 'large_default')|escape:'html':'UTF-8'}";
                            var html = '<img style="display: none;" src="' + img + '"  />';
                            $(".container_boule").html(html)                        }
                    </script>
            </span>
        {foreachelse}
            <span class="not-pot">
                <input type="radio" name="pot" id="not_pot" class="radio_pot" checked="" data-price="0" value="0" />
                <label for="not_pot">{l s="Aucun pot disponible"  mod='tunnelvente'}</label>
                <img style="display: none;"
                     src="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}modules/tunnelvente/images/pot/default-pot.png"/>
            </span>
        {/foreach}
        {if count($result) }
            <div class=" clearfix"></div>
            <span class="not-pot">
                <input type="radio" name="pot" id="not_pot" class="radio_pot" data-price="0" value="0" />
                <label for="not_pot">{l s="Je ne souhaite pas ajouter de cache pot"  mod='tunnelvente'}</label>
                <img style="display: none;"
                     src="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}modules/tunnelvente/images/pot/default-pot.png"/>
            </span>
        {/if}
    </div>
     <div class="icon-tunnel">
        <div class="cercle_ch_pot cercle"></div>
    </div>
     <div class="clear"></div>
    <div class="description">
        <div>
            {$product->description}
        </div>
    </div>
    <div class="btns_next_prev">
        <button type="button" class="prev">prev</button>
        <button type="submit" class="next">next</button>
    </div>
    <div class="loading"></div>
</form>


<script type="text/javascript">
    var baseurl_tunnelvente = "{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}module/tunnelvente/boule?back=1";
</script>
{literal}
<script type="text/javascript">
    $(function($){

        $('form#form_pot .prev').click(function(event){
            var $me = $(this),classe= 'isactive';
            if(!$me.hasClass(classe)){
                $me.addClass(classe);
                try{
                    $.ajax({
                    type: 'GET',
                    url: baseurl_tunnelvente,
                    data: 'ajax=1&back=1',
                    dataType: 'json',
                    success: function(json) {

                            if(json.hasError){

                                $.each(json.errors,function(k,v){
                                    showError(v);
                                });
                            }else{


                                $('#resp_content').html(json.html);
                                $('#my_errors').empty();
                                ShowHideStep(json.numStep);
                            }
                            $me.removeClass(classe);
                        }
                    });
                }catch(e){
                    $me.removeClass(classe);
                }
            }
        });


        $('form#form_pot').submit(function(event){
            event.preventDefault();
            var $me = $(this),classe= 'isactive';
            if(!$me.hasClass(classe)){
                $me.addClass(classe);

                try{
                    $.ajax({
                    type: 'POST',
                    url: $me.attr("action"),
                    data: 'ajax=1&'+$me.serialize(),
                    dataType: 'json',
                    success: function(json) {

                            if(json.hasError){

                                $.each(json.errors,function(k,v){
                                    showError(v);
                                });
                            }else{


                                $('#resp_content').html(json.html);
                                $('#my_errors').empty();
                                ShowHideStep(json.numStep);
                            }
                            $me.removeClass(classe);
                        }
                    });
                }catch(e){
                    $me.removeClass(classe);
                }

            }

        });
        //find all form with class jqtransform and apply the plugin
        $("#form_pot").jqTransform();

        $.each($("#form_pot input:radio.radio_pot"),function(){
            $(this).prev().css('background-color',$(this).data('bg'));
        });

        $("#form_pot input:radio.radio_pot").click(function(){
            var $me = $(this),$parent = $me.parents('span'),calPrice;
            $('form#form_pot label').removeClass("checked");
            if($parent.hasClass('not-pot')){
                $parent.find('label').addClass('checked');
            }
             /* pot block right*/
            if($parent.find('img').length){
                $('.thirdCol .container_pot img').attr('src',$parent.find('img').attr('src')).show();
                $('.thirdCol .textpotpromo').show();
                $('.thirdCol .text_p_contractuelle').show();
            }else{
                $('.thirdCol .container_pot img').hide();
                $('.thirdCol .textpotpromo,.thirdCol .text_p_contractuelle').hide();
            }
            /* price boule */
            calPrice = $('.thirdCol').data('calPrice');
            calPrice.setPricePot( $me.data('price') ,true);
            if(parseInt($me.val()) == 0){
                $('input#not_pot').attr('checked',true);
                $('form#form_pot').submit();
            }
        });

        $.each($('form#form_pot  input:checked').parents('span'),function(){
           $(this).find('input:radio').click();
        });

        if(!$('form#form_pot  input:checked').length  && $('form#form_pot input:radio').length){
            $('form#form_pot  input:radio:eq(0)').click();
        }

    });
</script>
{/literal}

{/if}