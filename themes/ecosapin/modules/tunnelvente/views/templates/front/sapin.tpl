<form action="{$urls.base_url}module/tunnelvente/recyclage" id="form_sapin" method="post">
    <ul>
    {foreach from=$result item=sapin}

        <li data-id="{$sapin.id_product_attribute}">
            <input type="radio" name="sapin" data-idp="{$sapin.id_product}" data-price="{$sapin.price_ttc}" value="{$sapin.id_product_attribute}" {if isset($id_product_sapin) and $sapin.id_product_attribute == $id_product_sapin } checked="" {/if} id="sapin_{$sapin.id_product_attribute}" />
            <label for="sapin_{$sapin.id_product_attribute}">{$sapin.name}</label>
            {if !empty($sapin.id_image)}
            <img style="display: none;" src="{$link->getImageLink($sapin.link_rewrite, $sapin.id_product|cat:'-'|cat:$sapin.id_image, 'large_default')|escape:'html':'UTF-8'}"  />
            {/if}
        </li>
    {/foreach}
    </ul>
    <div class="icon-tunnel">
        <div class="cercle_ch_essence cercle"></div>
        {*<h2>{l s="Choisissez l'essence de votre sapin" mod='tunnelvente'}</h2>*}
    </div> 
    <div class="clear"></div>
    <div class="description">
       {foreach from=$result item=sapin}
           <div class="description_{$sapin.id_product_attribute}" style="display: none">
               {$sapin.description}
           </div>
       {/foreach}
    </div>
    <div class="msg_info">
        <a title="Contactez-nous" target="_blank" href="{$urls.base_url}fr/contactez-nous"><h4>{l s="Demande spéciale, contactez nous ici" mod='tunnelvente'}</h4></a>
    </div>
    <div class="btns_next_prev">
        <button type="button" class="prev">prev</button>
        <button type="submit" class="next">next</button>
    </div>
    <div class="loading"></div>
</form>
    
<script type="text/javascript">
    var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/taille?back=2";
</script>     
{literal}
<script type="text/javascript">
    $(function($){
        
        $('form#form_sapin .prev').click(function(event){
            var $me = $(this),classe= 'isactive';
            if(!$me.hasClass(classe)){              
                $me.addClass(classe);
                try{
                        $.ajax({
                        type: 'GET',
                        url: baseurl_tunnelvente,
                        data: 'ajax=1&back=2',
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
        
        
        $('form#form_sapin').submit(function(event){
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
        
        $('form#form_sapin input:radio').click(function(){
            var $me = $(this),$parent = $me.parents("li") ,id = $parent.data('id'),calPrice;
            $('form#form_sapin .description').find("> div").hide()
                    .end()
                    .find(".description_"+id).show();
             
            /* image block right*/
            if($parent.find('img').length){
                $('.thirdCol .container_sapin img').attr('src',$parent.find('img').attr('src')).show();
            }
            
            /* label */
            $('form#form_sapin li label').removeClass("checked");
            $parent.find('label').addClass("checked");
            
            /* price sapin */
            calPrice = $('.thirdCol').data('calPrice');            
            calPrice.setPriceSapin( $me.data('price') ,true);
            
        });        
        
        $.each($('form#form_sapin li input:checked'),function(){
            $(this).click();
        });
        
        if( !$('form#form_sapin li input:checked').length && $('form#form_sapin li input:radio').length){            
            var sapinFraseri = $('form#form_sapin li input:radio').filter('[data-idp="54"]');//54 id_product Fraseri
            if(sapinFraseri.length){
                sapinFraseri.click();
            }else{
                $('form#form_sapin li input:radio:eq(0)').click();
            }
        }
        
    });
</script>
{/literal}