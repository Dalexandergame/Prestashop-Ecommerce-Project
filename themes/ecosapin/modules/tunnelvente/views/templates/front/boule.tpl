<form action="{$urls.base_url}module/tunnelvente/pot" id="form_boule" method="post">
     <h4>{l s="Choisissez le type de décoration" mod='tunnelvente'}</h4> 
    <div class="list_boule">
        {foreach from=$result item=boule}
            <span class="boule boule_{$boule.id_product_attribute}">
                <input style="display: none;" type="radio" name="boule" class="radio_boule" value="{$boule.id_product_attribute}" {if isset($id_product_boule) and $boule.id_product_attribute == $id_product_boule } checked="" {/if} title="{$boule.name}" data-idattribut="{$boule.id_attribute}" data-price="{$boule.price_ttc}" data-bg="{$boule.color}"/>
                {if !empty($boule.id_image)}
                <img class="eco_img" style="display: none;" src="{$link->getImageLink($boule.link_rewrite, $boule.id_product|cat:'-'|cat:$boule.id_image, 'large_default')|escape:'html':'UTF-8'}"  />
                {/if}
                {$boule.name}
{*                <img src="{$urls.base_url}img/co/{$boule.id_attribute}.jpg"/>*}
            </span>            
        {/foreach}    
    </div>
    <ul>

        <li data-id="0">
            <input type="radio" data-price="0" name="boule" value="0" id="boule_0"  />
            <label for="boule_0">{l s="Non merci j'ai mes propres boule" mod='tunnelvente'}</label>
             <img class="eco_img" style="display: none;" src="{$urls.base_url}modules/tunnelvente/views/img/bg-default-white.png" />
        </li>
    </ul>
    
    
    <div class="icon-tunnel">
        <div class="cercle_ch_boule cercle"></div>
        {*<h2>{l s="Choisissez le type de décoration" mod='tunnelvente'}</h2>*}
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
    {if $petit_sapin_suisse == 1}
        var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/sapin?back=1";
    {else}
        var baseurl_tunnelvente = "{$urls.base_url}module/tunnelvente/recyclage?back=1";
    {/if}
</script>     
{literal}
<script type="text/javascript">
    $(function($){
        
        $('form#form_boule .prev').click(function(event){
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
        
        
        $('form#form_boule').submit(function(event){
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
//        $("#form_boule").jqTransform();
        
         
        
        $('form#form_boule .list_boule .boule').click(function(){
            var $me = $(this),calPrice;
            $('form#form_boule .list_boule .boule').not($me).removeClass("checked");
            $me.find('input').attr('checked',true);
            $(this).addClass("checked");
            
            if($me.find('img.eco_img').length){
                $('.thirdCol .container_boule img').attr('src',$me.find('img.eco_img').attr('src')).show();
                $('.thirdCol .textboutpromo').show();
                $('.thirdCol .text_p_contractuelle').show();
            }else{
                $('.thirdCol .container_boule img,.thirdCol .text_p_contractuelle').hide();
            }
            $('form#form_boule li label').removeClass("checked");
            
            /* price boule */
            calPrice = $('.thirdCol').data('calPrice');
            calPrice.setPriceBoule( $me.find('input').data('price') ,true);
            
            /* show/hide description déco  */
            $('.description div > div').hide();
            $('#deco_'+$me.find('input').data('idattribut')).show();
            
        });
        
        $('form#form_boule li label').click(function(){
            var $me = $(this),$parent = $me.parents("li"),calPrice ;
            $('form#form_boule .list_boule .boule').removeClass("checked");
            $(this).addClass("checked");   
            $(this).prev().prop('checked',true);
            /* price boule */
            calPrice = $('.thirdCol').data('calPrice');  
            calPrice.setPriceBoule( $parent.find('input').data('price') ,true); 
            $('.thirdCol .container_boule img,.thirdCol .text_p_contractuelle').hide();
            $('.thirdCol .textboutpromo').hide();
            /* hide description déco  */
            $('.description div > div').hide();
            $('form#form_boule').submit();
        });
         $('form#form_boule input:radio:eq(0)').click();
        /*
        $.each($('form#form_boule .list_boule input:checked').parents('span'),function(){
            $(this).click();
        });
        
        if(!$('form#form_boule .list_boule input:checked').length && $('form#form_boule li input:checked').length){
            $('form#form_boule li label:eq(0)').click();
        }
        
        if( !$('form#form_boule input:checked').length && $('form#form_boule input:radio').length){
            $('form#form_boule input:radio:eq(0)').click();
        }
        //*/
    });
</script>
{/literal}
<style type="text/css">.description div > div{ display: none;}</style>